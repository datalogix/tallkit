import { dataSelector, queryData, eventName, generateId, isRtl, onLivewireCommit, getTransitionTimeout, toMilliseconds, isRendered, placeNextTo, pushEscapeLayer, removeEscapeLayer } from '../utils'
import { toggleable } from '../mixins/toggleable'

export function popover({ mode = 'hover', position = 'bottom', align = 'end', matchTriggerWidth = false, margin = 4, delay = 0 } = {}) {
  delay = toMilliseconds(delay)

  const _toggleable = toggleable()

  const usesClick = () => mode !== 'manual' && (window.matchMedia('(hover: none)').matches || mode === 'dropdown')

  return {
    ..._toggleable,

    popoverElement: null,
    trigger: null,
    ariaTrigger: null,

    resizeObserver: null,
    mutationObserver: null,
    livewireCommitCleanup: null,
    _syncObserver: null,
    _onBeforeToggle: null,
    _popoverId: null,
    _unbindTrigger: null,
    _stopOutsideClick: null,
    _rAF: null,
    _cancelPendingClose: null,
    _hoverCloseTimer: null,
    _hoverOpenTimer: null,

    mouseX: 0,
    mouseY: 0,
    _hasPointerPosition: false,

    init() {
      _toggleable.init.call(this)

      this._onBeforeToggle = (e) => {
        if (e.newState === 'open' && this.isPopoverReadonly()) {
          e.preventDefault()
          return
        }

        queueMicrotask(() => {
          if (e.newState === 'open') {
            this.onOpen()
          } else {
            this.onClose()
          }
        })
      }

      this.refreshPopover()

      this.livewireCommitCleanup = onLivewireCommit(({ succeed }) => {
        succeed(() => {
          if (!this.popoverElement?.matches(':popover-open')) return
          if (!this.$root?.isConnected) return

          this.boundSetPosition()
        })
      })

      // Livewire may replace the trigger or the panel and strip attributes added from JS: no stale references.
      this._syncObserver = new MutationObserver((records) => {
        const relevant = records.some((record) => record.type === 'childList'
          ? !this.popoverElement?.contains(record.target)
          : record.target === this.ariaTrigger || record.target === this.popoverElement)

        if (relevant) this.refreshPopover()
      })

      this._syncObserver.observe(this.$root, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['id', 'tabindex', 'aria-haspopup', 'aria-expanded', 'aria-controls', 'aria-describedby'],
      })
    },

    resolvePopoverElement() {
      const last = this.$root.lastElementChild

      return last?.matches('[popover]') ? last : null
    },

    resolvePopoverTrigger() {
      const first = this.$root.firstElementChild

      return first !== this.popoverElement ? first : this.$root
    },

    refreshPopover() {
      if (!this.$root?.isConnected) return

      const popoverElement = this.resolvePopoverElement()
      const popoverChanged = popoverElement !== this.popoverElement

      if (popoverChanged) {
        this.releasePopoverElement()
        this.popoverElement = popoverElement
        this.adoptPopoverElement()
      }

      if (!this.popoverElement) return

      const trigger = this.resolvePopoverTrigger()

      if (trigger && (trigger !== this.trigger || popoverChanged)) {
        this.trigger = trigger
        this.ariaTrigger = trigger.matches(dataSelector('control')) ? trigger : (queryData(trigger, 'control') ?? trigger)

        this.bindPopoverTrigger()

        if (this.isOpened()) this.boundSetPosition()
      }

      this.syncPopoverTrigger()
    },

    releasePopoverElement() {
      if (!this.popoverElement) return

      this.popoverElement.removeEventListener('beforetoggle', this._onBeforeToggle)

      this._cancelPendingClose?.()
      this.onClose()
    },

    adoptPopoverElement() {
      this.popoverElement?.addEventListener('beforetoggle', this._onBeforeToggle)
    },

    popoverRole() {
      return this.popoverElement?.getAttribute('role')
        ?? this.popoverElement?.querySelector('[role=menu], [role=listbox], [role=dialog]')?.getAttribute('role')
        ?? null
    },

    syncPopoverTrigger() {
      const el = this.ariaTrigger
      const popoverElement = this.popoverElement

      if (!el || !popoverElement) return

      // Stable across replacements, so aria-controls/describedby never dangle.
      if (!popoverElement.id) {
        popoverElement.id = (this._popoverId ??= generateId('popover'))
      }

      const set = (name, value) => {
        if (el.getAttribute(name) !== value) el.setAttribute(name, value)
      }
      const role = this.popoverRole()

      if (this.triggerTakesPopupAria()) {
        if (!el.hasAttribute('aria-haspopup')) {
          set('aria-haspopup', role === 'listbox' || role === 'dialog' ? role : 'true')
        }

        set('aria-expanded', this.isOpened() ? 'true' : 'false')
      }

      const current = el.getAttribute('aria-controls')
      const controlled = current ? document.getElementById(current) : null

      if (!controlled) {
        set('aria-controls', popoverElement.id)
      }

      if (
        mode === 'context'
        && !usesClick()
        && !this.trigger.hasAttribute('tabindex')
        && !['A', 'BUTTON', 'INPUT', 'SELECT', 'TEXTAREA'].includes(this.trigger.tagName)
      ) {
        this.trigger.setAttribute('tabindex', '0')
      }
    },

    triggerTakesPopupAria() {
      return !!this.ariaTrigger?.matches('a[href], button, select, [role]:not([role=none]):not([role=presentation])')
    },

    setPopoverExpanded(expanded) {
      if (!this.ariaTrigger || !this.triggerTakesPopupAria()) return

      const value = expanded ? 'true' : 'false'

      if (this.ariaTrigger.getAttribute('aria-expanded') !== value) {
        this.ariaTrigger.setAttribute('aria-expanded', value)
      }
    },

    // Plain listeners: Alpine.bind resolves `this` from the closest x-data, which may not be ours.
    bindPopoverTrigger() {
      this.unbindPopoverTrigger()

      const trigger = this.trigger
      const cleanups = []
      const on = (target, type, handler) => {
        target.addEventListener(type, handler)
        cleanups.push(() => target.removeEventListener(type, handler))
      }

      if (usesClick()) {
        // Enter or Space make a click with detail 0.
        on(trigger, 'click', (event) => this.toggle(this.popoverRole() !== 'menu' || event.detail === 0))

        on(trigger, 'keydown', (event) => {
          if (!['ArrowDown', 'ArrowUp'].includes(event.key) || this.isOpened()) return

          event.preventDefault()
          this.open(event.key === 'ArrowUp' ? 'last' : true)
        })

        const leaves = (event) => {
          const to = event.relatedTarget

          if (!(to instanceof Element) || !this.isOpened()) return
          if (this.trigger?.contains(to) || this.popoverElement?.contains(to)) return

          this.close()
        }

        on(trigger, 'focusout', leaves)
        if (this.popoverElement) on(this.popoverElement, 'focusout', leaves)
      } else if (mode === 'hover') {
        on(trigger, 'mouseenter', () => this.hoverOpen())
        on(trigger, 'mouseleave', () => this.hoverClose())

        // Focus stays on the trigger: losing it would close the panel.
        const holdsFocus = (el) => !!el && (this.trigger?.contains(el) || this.popoverElement?.contains(el))

        on(trigger, 'focusin', (event) => {
          if (!event.target.matches?.(':focus-visible')) return

          this.cancelHoverClose()
          this.open(false)
        })
        on(trigger, 'focusout', (event) => {
          if (!holdsFocus(event.relatedTarget)) this.close()
        })

        if (this.popoverElement) {
          on(this.popoverElement, 'focusout', (event) => {
            if (!holdsFocus(event.relatedTarget)) this.close()
          })

          on(this.popoverElement, 'mouseenter', () => {
            this.cancelHoverClose()

            if (this._cancelPendingClose) this.open(false)
          })
          on(this.popoverElement, 'mouseleave', () => this.hoverClose())
        }
      } else if (mode === 'context') {
        on(trigger, 'contextmenu', (event) => {
          event.preventDefault()
          this.close()
          this.mouseX = event.clientX
          this.mouseY = event.clientY
          this._hasPointerPosition = true
          this.open()
        })

        on(trigger, 'keydown', (event) => {
          if (event.key !== 'ContextMenu' && !(event.shiftKey && event.key === 'F10')) return

          event.preventDefault()
          this.close()
          this._hasPointerPosition = false
          this.open()
        })
      }

      on(trigger, eventName('open'), () => this.open())
      on(trigger, eventName('close'), () => this.close())

      this._unbindTrigger = () => cleanups.forEach((cleanup) => cleanup())
    },

    unbindPopoverTrigger() {
      this.cancelHoverOpen()
      this.cancelHoverClose()
      this._unbindTrigger?.()
      this._unbindTrigger = null
    },

    hoverOpen() {
      this.cancelHoverClose()
      this.cancelHoverOpen()

      if (!delay || this.isOpened() || this._cancelPendingClose) {
        this.open(false)
        return
      }

      this._hoverOpenTimer = setTimeout(() => {
        this._hoverOpenTimer = null
        this.open(false)
      }, delay)
    },

    cancelHoverOpen() {
      clearTimeout(this._hoverOpenTimer)
      this._hoverOpenTimer = null
    },

    hoverClose() {
      this.cancelHoverOpen()
      this.cancelHoverClose()
      this._hoverCloseTimer = setTimeout(() => this.close(), 100)
    },

    cancelHoverClose() {
      clearTimeout(this._hoverCloseTimer)
      this._hoverCloseTimer = null
    },

    listenOutsideClick() {
      this.stopOutsideClick()

      const handler = (e) => {
        if (!e.target?.isConnected) return

        if (usesClick()) {
          if (this.trigger?.contains(e.target)) return

          if ((
            this.popoverElement?.hasAttribute('data-keep-open')
            || e.target.hasAttribute('data-keep-open')
            || e.target.closest('[data-keep-open]')
          ) && this.popoverElement?.contains(e.target)) {
            return
          }
        } else if (mode === 'context') {
          if (this.popoverElement?.contains(e.target)) return
        } else {
          return
        }

        this.close()
      }

      document.addEventListener('click', handler)
      this._stopOutsideClick = () => document.removeEventListener('click', handler)
    },

    stopOutsideClick() {
      this._stopOutsideClick?.()
      this._stopOutsideClick = null
    },

    destroy() {
      this.onClose()
      this.stopOutsideClick()
      this.unbindPopoverTrigger()
      this.livewireCommitCleanup?.()
      this._syncObserver?.disconnect()
      this.popoverElement?.removeEventListener('beforetoggle', this._onBeforeToggle)
    },

    isPopoverReadonly() {
      return this.ariaTrigger?.getAttribute('aria-readonly') === 'true'
    },

    open(focus = true) {
      if (this.isPopoverReadonly()) return

      requestAnimationFrame(() => {
        if (!this.popoverElement?.isConnected) this.refreshPopover()
        if (!this.popoverElement?.isConnected) return

        if (this._cancelPendingClose) {
          this._cancelPendingClose()
          this._cancelPendingClose = null
          this.onOpen()
        } else {
          if (this.popoverElement.matches(':popover-open')) return
          this.popoverElement.showPopover()
        }

        // Next tick: neither focus() nor scrolling works on a hidden element.
        this.$nextTick(() => requestAnimationFrame(() => {
          if (!this.popoverElement?.matches(':popover-open')) return

          const chosen = this.popoverElement.querySelector('[role=option][data-active]:not([data-active="false"]), [role=option][aria-selected="true"]')

          chosen?.scrollIntoView({ block: 'nearest' })

          if (!focus) return

          const items = Array.from(this.popoverElement.querySelectorAll(
            '[role=menuitem], [role=menuitemcheckbox], [role=menuitemradio], [role=option], [role=tab], [role=gridcell][tabindex="0"]'
          )).filter((item) => !item.disabled && item.getAttribute('aria-disabled') !== 'true' && isRendered(item))

          const selected = focus === 'last' ? null : items.find((item) => item.getAttribute('aria-selected') === 'true')

          ;(selected ?? (focus === 'last' ? items.at(-1) : items[0]) ?? this.popoverElement).focus()
        }))
      })
    },

    // Hidden after the leave transition, not in the middle of it.
    close() {
      this.cancelHoverOpen()

      requestAnimationFrame(() => {
        if (!this.popoverElement?.isConnected) return
        if (!this.popoverElement.matches(':popover-open')) return
        if (this._cancelPendingClose) return

        this.onClose()

        const target = this.popoverElement.firstElementChild ?? this.popoverElement

        let fallback

        const hide = (event) => {
          // Only its own transitionend: a child's bubbles up too.
          if (event && event.target !== target) return

          target.removeEventListener('transitionend', hide)
          clearTimeout(fallback)
          this._cancelPendingClose = null

          if (this.popoverElement?.isConnected && this.popoverElement.matches(':popover-open')) {
            // Focus inside a hidden panel would be lost: back to the trigger.
            const hadFocus = this.popoverElement.contains(document.activeElement)

            this.popoverElement.hidePopover()

            if (hadFocus) this.ariaTrigger?.focus?.()
          }
        }

        this._cancelPendingClose = () => {
          target.removeEventListener('transitionend', hide)
          clearTimeout(fallback)
          this._cancelPendingClose = null
        }

        requestAnimationFrame(() => {
          const timeout = getTransitionTimeout(target)

          if (timeout === 0) {
            hide()
            return
          }

          target.addEventListener('transitionend', hide)
          fallback = setTimeout(hide, timeout + 50)
        })
      })
    },

    onOpen() {
      pushEscapeLayer(this)
      _toggleable.open.call(this)
      this.setPopoverExpanded(true)
      this.listenOutsideClick()

      this._onScroll ??= () => this.boundSetPosition()
      this._onResize ??= () => this.boundSetPosition()

      window.addEventListener('scroll', this._onScroll, true)
      window.addEventListener('resize', this._onResize, true)

      this.resizeObserver = new ResizeObserver(() => this.boundSetPosition())
      this.resizeObserver.observe(this.trigger)
      this.resizeObserver.observe(this.popoverElement)

      this.mutationObserver = new MutationObserver(() => this.boundSetPosition())
      this.mutationObserver.observe(this.trigger, {
        childList: true
      })

      this.mutationObserver.observe(this.popoverElement, {
        childList: true
      })

      this.setPosition()
    },

    onClose() {
      removeEscapeLayer(this)

      if (this.isClosed()) return

      _toggleable.close.call(this)
      this.setPopoverExpanded(false)
      this.stopOutsideClick()

      window.removeEventListener('scroll', this._onScroll, true)
      window.removeEventListener('resize', this._onResize, true)

      this.resizeObserver?.disconnect()
      this.resizeObserver = null

      this.mutationObserver?.disconnect()
      this.mutationObserver = null

      if (this._rAF) {
        cancelAnimationFrame(this._rAF)
        this._rAF = null
      }
    },

    setPosition() {
      if (!this.popoverElement?.isConnected) return
      if (!this.popoverElement.matches(':popover-open')) return

      const usesTriggerRect = mode !== 'context' || !this._hasPointerPosition
      if (usesTriggerRect && !this.trigger?.isConnected) return

      let triggerRect

      if (mode === 'context' && this._hasPointerPosition) {
        triggerRect = {
          top: this.mouseY,
          bottom: this.mouseY,
          left: this.mouseX,
          right: this.mouseX,
          height: 0,
          width: 0,
        }
      } else {
        triggerRect = this.trigger.getBoundingClientRect()
      }

      if (matchTriggerWidth) {
        this.popoverElement.style.width = `${triggerRect.width}px`
      }

      placeNextTo(this.popoverElement, triggerRect, { position, align, margin, rtl: isRtl(this.trigger) })
    },

    boundSetPosition() {
      if (this._rAF) return

      this._rAF = requestAnimationFrame(() => {
        this.setPosition()
        this._rAF = null
      })
    }
  }
}

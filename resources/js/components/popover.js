import { dataSelector, queryData, eventName, generateId, isRtl, onLivewireCommit, keepAttributesOnMorph, getTransitionTimeout, toMilliseconds, isRendered, placeNextTo, pushEscapeLayer, removeEscapeLayer } from '../utils'
import { toggleable } from '../mixins/toggleable'

const LONG_PRESS = 500

const claimedEvents = new WeakSet()

export function popover({ mode = 'hover', position = 'bottom', align = 'end', matchTriggerWidth = false, margin = 4, delay = 0 } = {}) {
  delay = toMilliseconds(delay)

  const _toggleable = toggleable()

  const usesClick = () => !['manual', 'context'].includes(mode) && (window.matchMedia('(hover: none)').matches || mode === 'dropdown')

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

    _pointerOffset: null,
    _contextEvent: null,

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

    bindPopoverTrigger() {
      this.unbindPopoverTrigger()

      const trigger = this.trigger
      const cleanups = []
      const on = (target, type, handler) => {
        target.addEventListener(type, handler)
        cleanups.push(() => target.removeEventListener(type, handler))
      }

      if (usesClick()) {
        on(trigger, 'click', (event) => this.toggle(this.popoverRole() !== 'menu' || event.detail === 0))

        on(trigger, 'keydown', (event) => {
          if (!['ArrowDown', 'ArrowUp'].includes(event.key) || (this.isOpened() && this.popoverRole() !== 'menu')) return

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
        let pressTimer = null
        let pressStart = null
        let pressed = false

        const cancelPress = () => {
          clearTimeout(pressTimer)
          pressTimer = null
        }

        const openAt = ({ clientX, clientY }) => {
          const rect = trigger.getBoundingClientRect()

          this._pointerOffset = { x: clientX - rect.left, y: clientY - rect.top }

          if (this.isOpened()) this.boundSetPosition()
          else this.open()
        }

        cleanups.push(cancelPress, keepAttributesOnMorph((el) => el === trigger, ['tabindex']))

        on(trigger, 'contextmenu', (event) => {
          if (claimedEvents.has(event)) return

          claimedEvents.add(event)
          this._contextEvent = event
          event.preventDefault()
          cancelPress()

          if (pressed) return

          openAt(event)
        })

        on(trigger, 'pointerdown', (event) => {
          pressed = false
          cancelPress()

          if (event.pointerType !== 'touch' || claimedEvents.has(event)) return

          claimedEvents.add(event)
          pressStart = { clientX: event.clientX, clientY: event.clientY }
          pressTimer = setTimeout(() => {
            pressTimer = null
            pressed = true
            openAt(pressStart)
          }, LONG_PRESS)
        })

        on(trigger, 'pointermove', (event) => {
          if (pressTimer && Math.hypot(event.clientX - pressStart.clientX, event.clientY - pressStart.clientY) > 10) cancelPress()
        })
        on(trigger, 'pointerup', cancelPress)
        on(trigger, 'pointercancel', cancelPress)

        on(trigger, 'click', (event) => {
          if (!pressed) return

          pressed = false
          event.preventDefault()
          event.stopPropagation()
        })

        on(trigger, 'keydown', (event) => {
          if (event.key !== 'ContextMenu' && !(event.shiftKey && event.key === 'F10')) return
          if (claimedEvents.has(event)) return

          claimedEvents.add(event)
          event.preventDefault()
          this._pointerOffset = null

          if (this.isOpened()) this.boundSetPosition()
          else this.open()
        })
      }

      on(trigger, eventName('open'), () => {
        this._pointerOffset = null
        this.open()
      })
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
          if (e === this._contextEvent) return
          if (this.popoverElement?.contains(e.target) && (e.type === 'contextmenu' || this.popoverElement.hasAttribute('data-keep-open') || e.target.closest('[data-keep-open]'))) return
        } else {
          return
        }

        this.close()
      }

      const types = mode === 'context' ? ['click', 'contextmenu'] : ['click']

      types.forEach((type) => document.addEventListener(type, handler))
      this._stopOutsideClick = () => types.forEach((type) => document.removeEventListener(type, handler))
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
        } else if (this.popoverElement.matches(':popover-open')) {
          // Already open: only a call that asks for focus goes on, e.g. arrows after a mouse open.
          if (!focus) return
        } else {
          this.popoverElement.showPopover()
        }

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
          if (event && event.target !== target) return

          target.removeEventListener('transitionend', hide)
          clearTimeout(fallback)
          this._cancelPendingClose = null

          if (this.popoverElement?.isConnected && this.popoverElement.matches(':popover-open')) {
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

      if (!this.trigger?.isConnected) return

      let triggerRect = this.trigger.getBoundingClientRect()

      if (mode === 'context' && this._pointerOffset) {
        const x = triggerRect.left + this._pointerOffset.x
        const y = triggerRect.top + this._pointerOffset.y

        triggerRect = { top: y, bottom: y, left: x, right: x, height: 0, width: 0 }
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

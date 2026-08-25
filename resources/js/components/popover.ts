import { dataKey, bind, generateId, isRtl, onLivewireCommit, getTransitionTimeout } from '../utils'
import { toggleable } from '../mixins/toggleable'

export function popover ({ mode = 'hover', position = 'bottom', align = 'end' } = {}) {
  const _toggleable = toggleable()

  return {
    ..._toggleable,

    popoverElement: null,
    trigger: null,

    resizeObserver: null,
    mutationObserver: null,
    livewireCommitCleanup: null,
    _rAF: null,
    _cancelPendingClose: null,

    mouseX: 0,
    mouseY: 0,
    _hasPointerPosition: false,

    init() {
      _toggleable.init.call(this)

      this.popoverElement = this.$root.lastElementChild?.matches('[popover]') && this.$root.lastElementChild

      if (!this.popoverElement) return

      this.trigger = this.$root.firstElementChild !== this.popoverElement ? this.$root.firstElementChild : this.$root

      if (this.trigger?.matches(dataKey('tooltip'))) {
         this.trigger = this.trigger.firstElementChild
      }

      const role = this.popoverElement.getAttribute('role')

      if (!this.trigger.hasAttribute('aria-haspopup') && role !== 'tooltip') {
        this.trigger.setAttribute('aria-haspopup', role === 'listbox' || role === 'dialog' ? role : 'true')
      }

      if (!this.trigger.hasAttribute('aria-expanded')) {
        this.trigger.setAttribute('aria-expanded', 'false')
      }

      if (!this.popoverElement.id) {
        this.popoverElement.id = generateId('popover')
      }

      if (!this.trigger.hasAttribute('aria-controls')) {
        this.trigger.setAttribute('aria-controls', this.popoverElement.id)
      }

      if (role === 'tooltip') {
        const ids = new Set((this.trigger.getAttribute('aria-describedby') ?? '').split(' ').filter(Boolean))
        ids.add(this.popoverElement.id)
        this.trigger.setAttribute('aria-describedby', Array.from(ids).join(' '))
      }

      this.popoverElement.addEventListener('beforetoggle', (e) => {
        queueMicrotask(() => {
          if (e.newState === 'open') {
            this.onOpen()
          } else {
            this.onClose()
          }
        })
      })

      this.livewireCommitCleanup = onLivewireCommit(({ succeed }) => {
        succeed(() => {
          if (!this.popoverElement?.matches(':popover-open')) return
          if (!this.$root?.isConnected) return

          this.boundSetPosition()
        })
      })

      if (mode !== 'manual' && (window.matchMedia('(hover: none)').matches || mode === 'dropdown')) {
        bind(this.trigger, {
          ['@click']() {
            this.toggle(!['menu'].includes(role))
          },

          ['@click.outside'](e) {
            if ((
              this.popoverElement.hasAttribute('data-keep-open')
              || e.target?.hasAttribute('data-keep-open')
              || e.target?.closest('[data-keep-open]')
            ) && this.popoverElement.contains(e.target)) {
              return
            }

            this.close()
          },
        })
      } else if (mode === 'hover') {
        bind(this.trigger, {
          ['@mouseenter']() {
            this.open(false)
          },

          ['@mouseleave']() {
            this.close()
          },

          ['@focus']() {
            this.open()
          },

          ['@blur']() {
            this.close()
          },
        })
      } else if (mode === 'context') {
        if (!this.trigger.hasAttribute('tabindex') && !['A', 'BUTTON', 'INPUT', 'SELECT', 'TEXTAREA'].includes(this.trigger.tagName)) {
          this.trigger.setAttribute('tabindex', '0')
        }

        bind(this.trigger, {
          ['@contextmenu.prevent'](event) {
            this.close()
            this.mouseX = event.clientX
            this.mouseY = event.clientY
            this._hasPointerPosition = true
            this.open()
          },

          ['@keydown'](event) {
            if (event.key !== 'ContextMenu' && !(event.shiftKey && event.key === 'F10')) return

            event.preventDefault()
            this.close()
            this._hasPointerPosition = false
            this.open()
          },
        })

        bind(this.popoverElement, {
          ['@click.outside']() {
            this.close()
          },
        })
      }

      bind(this.trigger, {
        ['@open']() {
          this.open()
        },

        ['@close']() {
          this.close()
        },

        ['@keydown.escape.window']() {
          this.close()
        },
      })
    },

    destroy() {
      this.onClose()
      this.livewireCommitCleanup?.()
    },

    open(focus = true) {
      requestAnimationFrame(() => {
        if (!this.popoverElement?.isConnected) return

        if (this._cancelPendingClose) {
          this._cancelPendingClose()
          this._cancelPendingClose = null
          this.onOpen()
        } else {
          if (this.popoverElement.matches(':popover-open')) return
          this.popoverElement.showPopover()
        }

        if (focus) {
          const firstItem = this.popoverElement.querySelector('[role=menuitem], [role=option], [role=tab]')
          ;(firstItem ?? this.popoverElement).focus()
        }
      })
    },

    close() {
      requestAnimationFrame(() => {
        if (!this.popoverElement?.isConnected) return
        if (!this.popoverElement.matches(':popover-open')) return
        if (this._cancelPendingClose) return

        this.onClose()

        const target = this.popoverElement.firstElementChild ?? this.popoverElement

        let fallback

        const hide = () => {
          target.removeEventListener('transitionend', hide)
          clearTimeout(fallback)
          this._cancelPendingClose = null

          if (this.popoverElement?.isConnected && this.popoverElement.matches(':popover-open')) {
            this.popoverElement.hidePopover()
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

          target.addEventListener('transitionend', hide, { once: true })
          fallback = setTimeout(hide, timeout + 50)
        })
      })
    },

    onOpen() {
      _toggleable.open.call(this)
      this.trigger.setAttribute('aria-expanded', 'true')

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
      if (this.isClosed()) return

      _toggleable.close.call(this)
      this.trigger.setAttribute('aria-expanded', 'false')

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

      const triggerHeight = triggerRect.height
      const triggerWidth = triggerRect.width
      const scrollTop = window.scrollY
      const scrollLeft = window.scrollX

      const tooltipHeight = this.popoverElement.offsetHeight
      const tooltipWidth = this.popoverElement.offsetWidth
      const isRTL = isRtl(this.trigger)
      const margin = 4

      const resolveAlign = (align: string) => {
        if (align === 'start') return isRTL ? 'right' : 'left'
        if (align === 'end') return isRTL ? 'left' : 'right'
        return align
      }

      const getCenterOffset = (pos: string, align: string) => {
        align = resolveAlign(align)

        if (align === 'left') return 0
        if (align === 'right') {
          return pos === 'left' || pos === 'right'
            ? triggerHeight - tooltipHeight
            : triggerWidth - tooltipWidth
        }

        return pos === 'left' || pos === 'right'
          ? (triggerHeight - tooltipHeight) / 2
          : (triggerWidth - tooltipWidth) / 2
      }

      const getCoords = (pos: string, align: string) => {
        const center = getCenterOffset(pos, align)
        let top = 0, left = 0

        switch (pos) {
          case 'right':
            left = triggerRect.right + margin + scrollLeft
            top = triggerRect.top + center + scrollTop
            break
          case 'left':
            left = triggerRect.left - tooltipWidth - margin + scrollLeft
            top = triggerRect.top + center + scrollTop
            break
          case 'bottom':
            top = triggerRect.bottom + margin + scrollTop
            left = triggerRect.left + center + scrollLeft
            break
          case 'top':
            top = triggerRect.top - tooltipHeight - margin + scrollTop
            left = triggerRect.left + center + scrollLeft
            break
        }

        return { top, left }
      }

      const isVisible = ({ top, left }) => {
          return (
            top >= scrollTop &&
            left >= scrollLeft &&
            top + tooltipHeight <= scrollTop + window.innerHeight &&
            left + tooltipWidth <= scrollLeft + window.innerWidth
          )
      }

      const positions = ['top', 'bottom', 'left', 'right']
      const aligns = ['start', 'left', 'end', 'right', 'center']
      let computedPosition = position || 'bottom'
      let computedAlign = align || 'end'
      let coords = getCoords(computedPosition, computedAlign)

      if (!isVisible(coords)) {
        let found = false

        for (const pos of [computedPosition, ...positions.filter(p => p !== computedPosition)]) {
          for (const al of [computedAlign, ...aligns.filter(a => a !== computedAlign)]) {
            const testCoords = getCoords(pos, al)
            if (isVisible(testCoords)) {
              computedPosition = pos
              computedAlign = al
              coords = testCoords
              found = true
              break
            }
          }

          if (found) {
            break
          }
        }
      }

      this.popoverElement.style.position = 'absolute'
      this.popoverElement.style.inset = 'auto'
      this.popoverElement.style.top = `${coords.top}px`
      this.popoverElement.style.left = `${coords.left}px`
      this.popoverElement.dataset.position = computedPosition
      this.popoverElement.dataset.align = computedAlign === 'center' ? 'center' : resolveAlign(computedAlign)
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

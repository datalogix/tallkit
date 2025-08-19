import { bind } from "./bind"
import { toggleable } from "./toggleable"

export function popover ({ mode, position, align }) {
  const _toggleable = toggleable()
  let _rAF: number | null = null

  const component = {
    ..._toggleable,

    popover: null,
    trigger: null,
    isTouch: window.matchMedia('(hover: none)').matches,

    init() {
      _toggleable.init.call(this)
      component.popover = this.popover = this.$el.lastElementChild?.matches('[popover]') && this.$el.lastElementChild
      component.trigger = this.trigger = this.$el.firstElementChild !== this.popover ? this.$el.firstElementChild : this.$el

      if (!this.popover) return

      this.$el.setAttribute('aria-haspopup', 'true')
      this.$el.setAttribute('aria-expanded', 'false')

      this.popover.addEventListener('beforetoggle', (e) => {
        queueMicrotask(() => {
          if (e.newState === 'open') {
            this.onOpen()
          } else {
            this.onClose()
          }
        })
      })

      if (this.isTouch || mode === 'dropdown') {
        bind(this.trigger, {
          ['@click']() {
            this.toggle()
          },

          ['@click.outside'](e) {
            if ((
              this.popover.hasAttribute('data-keep-open')
              || e.target.hasAttribute('data-keep-open')
              || e.target?.parentElement.hasAttribute('data-keep-open')
            ) && this.popover.contains(e.target)) {
              return
            }

            this.close()
          },

          ['@keyup.escape.window']() {
            this.close()
          },
        })
      } else if (mode !== 'manual') {
        bind(this.trigger, {
          ['@mouseenter']() {
            this.open()
          },

          ['@mouseleave']() {
            this.close()
          },

          ['@keyup.escape.window']() {
            this.close()
          },
        })
      }
    },

    open() {
      this.popover.showPopover()
    },

    close() {
      this.popover.hidePopover()
    },

    onOpen() {
      _toggleable.open.call(this)
      this.$el.setAttribute('aria-expanded', 'true')

      window.addEventListener('scroll', this.boundSetPosition, true)

      this.resizeObserver = new ResizeObserver(this.boundSetPosition)
      this.resizeObserver.observe(this.trigger)
      this.resizeObserver.observe(this.popover)

      this.mutationObserver = new MutationObserver(this.boundSetPosition)
      this.mutationObserver.observe(this.trigger, {
        childList: true,
        subtree: true
      })

      this.mutationObserver.observe(this.popover, {
        childList: true,
        subtree: true
      })

      this.setPosition()
    },

    onClose() {
      _toggleable.close.call(this)
      this.$el.setAttribute('aria-expanded', 'false')
      window.removeEventListener('scroll', this.boundSetPosition, true)

      this.resizeObserver?.disconnect()
      this.resizeObserver = null

      this.mutationObserver?.disconnect()
      this.mutationObserver = null
    },

    boundSetPosition() {
      if (_rAF) return

      _rAF = requestAnimationFrame(() => {
        this.setPosition()
        _rAF = null
      })
    },

    setPosition() {
      const scrollTop = window.scrollY
      const scrollLeft = window.scrollX
      const triggerRect = this.trigger.getBoundingClientRect()
      const triggerHeight = triggerRect.height
      const triggerWidth = triggerRect.width
      const tooltipHeight = this.popover.offsetHeight
      const tooltipWidth = this.popover.offsetWidth
      const margin = 4

      const getCenterOffset = (pos: string, align: string) => {
        if (align === 'start' || align === 'left') return 0
        if (align === 'end' || align === 'right') {
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

      this.popover.style.position = 'absolute'
      this.popover.style.inset = 'auto'
      this.popover.style.top = `${coords.top}px`
      this.popover.style.left = `${coords.left}px`
      this.popover.dataset.position = computedPosition
      this.popover.dataset.align = computedAlign
    },
  }

  component.boundSetPosition = component.boundSetPosition.bind(component)

  return component
}

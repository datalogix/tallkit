import { popover } from "../utils"

export function tooltip({ position, align }) {
  const _popover = popover()
  let _rAF: number | null = null

  const component = {
    ..._popover,

    init() {
      _popover.init.call(this)

      window.Alpine.bind(this.trigger, {
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
    },

    open() {
      _popover.open.call(this)

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

    close() {
      _popover.close.call(this)

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
      const triggerHeight = this.trigger.offsetHeight
      const triggerWidth = this.trigger.offsetWidth
      const tooltipHeight = this.popover.offsetHeight
      const tooltipWidth = this.popover.offsetWidth

      let center = 0

      if (align !== 'start') {
        if (position === 'left' || position === 'right') {
          center = align === 'end'
            ? triggerHeight - tooltipHeight
            : (triggerHeight / 2) - (tooltipHeight / 2)
        } else {
          center = align === 'end'
            ? triggerWidth - tooltipWidth
            : (triggerWidth / 2) - (tooltipWidth / 2)
        }
      }

      const margin = 4
      let top = 0
      let left = 0
      let computedPosition = position

      const setCoords = (pos: string) => {
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
      }

      setCoords(computedPosition)

      const absoluteBottom = top + tooltipHeight
      const absoluteRight = left + tooltipWidth

      if (computedPosition === 'top' && top < scrollTop) {
        computedPosition = 'bottom'
        setCoords(computedPosition)
      } else if (computedPosition === 'bottom' && absoluteBottom > scrollTop + window.innerHeight) {
        computedPosition = 'top'
        setCoords(computedPosition)
      }

      if (computedPosition === 'left' && left < scrollLeft) {
        computedPosition = 'right'
        setCoords(computedPosition)
      } else if (computedPosition === 'right' && absoluteRight > scrollLeft + window.innerWidth) {
        computedPosition = 'left'
        setCoords(computedPosition)
      }

      this.popover.style.position = 'absolute'
      this.popover.style.inset = 'auto'
      this.popover.style.top = `${top}px`
      this.popover.style.left = `${left}px`
    },
  }

  component.boundSetPosition = component.boundSetPosition.bind(component)

  return component
}

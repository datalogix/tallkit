import { popover } from "../utils"

export function tooltip ({ position, align }) {
  const _popover = popover()

  return {
    ..._popover,

    trigger(toggleable = false) {
      if (toggleable) {
        return {
          ['@click']() {
            this.toggle()
          },

          ['@click.outside']() {
            this.close()
          }
        }
      }

      return {
        ['@mouseenter']() {
          this.open()
        },

        ['@mouseleave']() {
          this.close()
        }
      }
    },

    open() {
      _popover.open.call(this)
      this.setPosition()
    },

    setPosition() {
      this.popover.style.position = 'absolute'

      const triggerRect = this.$el.getBoundingClientRect()
      const parentRect = this.$el.offsetParent.getBoundingClientRect()

      const triggerHeight = this.$el.offsetHeight
      const triggerWidth = this.$el.offsetWidth
      const tooltipHeight = this.popover.offsetHeight
      const tooltipWidth = this.popover.offsetWidth

      const offsetLeft = triggerRect.left - parentRect.left
      const offsetRight = triggerRect.right - parentRect.left
      const offsetTop = triggerRect.top - parentRect.top
      const offsetBottom = triggerRect.bottom - parentRect.top

      let center = 0

      if (align !== 'start') {
        if (position === 'left' || position === 'right') {
          center = align === 'end' ? triggerHeight - tooltipHeight : (triggerHeight / 2) - (tooltipHeight / 2)
        } else {
          center = align === 'end' ? triggerWidth - tooltipWidth : (triggerWidth / 2) - (tooltipWidth / 2)
        }
      }

      let top = 0
      let left = 0

      if (position === 'right') {
        left = offsetRight + 4
        top = offsetTop + center
      } else if (position === 'left') {
        top = offsetTop + center
        left = offsetLeft - tooltipWidth - 4
      } else if (position === 'bottom') {
        top = offsetBottom + 4
        left = offsetLeft + center
      } else {
        top = offsetTop - tooltipHeight - 4
        left = offsetLeft + center
      }

      this.popover.style.inset = `${top}px auto auto ${left}px`;
    },
  }
}

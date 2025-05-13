import { toggleable } from "./toggleable"

export function popover () {
  const _toggleable = toggleable()

  return {
    ..._toggleable,

    popover: null,

    init() {
      _toggleable.init.call(this)
      this.popover = this.$el.querySelector('[popover]')
    },

    open() {
      this.popover.showPopover()
      _toggleable.open.call(this)
    },

    close() {
      this.popover.hidePopover()
      _toggleable.close.call(this)
    },
  }
}

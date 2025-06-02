import { toggleable } from "./toggleable"

export function popover () {
  const _toggleable = toggleable()

  return {
    ..._toggleable,

    popover: null,
    trigger: null,

    init() {
      _toggleable.init.call(this)
      this.popover = this.$el.lastElementChild?.matches('[popover]') && this.$el.lastElementChild
      this.trigger = this.$el.firstElementChild !== this.popover ? this.$el.firstElementChild : this.$el
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

import { bind } from "../utils"

export function inputCopyable() {
  return {
    copied: false,
    timeout: null,

    init() {
      const input = this.$el.closest('[data-tallkit-input]').querySelector('input')

      if (!input) {
        return
      }

      bind(this.$el, {
        ['@click']() {
          clearTimeout(this.timeout)
          this.copied = true
          this.popover && this.popover.showPopover()

          navigator.clipboard && navigator.clipboard.writeText(input.value)

          this.timeout = setTimeout(() => {
            this.popover && this.popover.hidePopover()
            this.copied = false
          }, 1000)
        }
      })
    }
  }
}

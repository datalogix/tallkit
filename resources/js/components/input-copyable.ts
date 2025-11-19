import { bind } from "../utils"

export function inputCopyable() {
  return {
    copied: false,
    timeout: null,

    get input () {
      return this.$el.closest('[data-tallkit-input]').querySelector('input')
    },

    init() {
      if (!this.input) {
        return
      }

      bind(this.$el, {
        async ['@click']() {
          clearTimeout(this.timeout)

          this.copied = true
          this.popoverElement && this.popoverElement.showPopover()

          if (navigator.clipboard) {
            await navigator.clipboard.writeText(this.input.value)
            this.input.dispatchEvent(new Event('copied', { bubbles: false }))
          }

          this.timeout = setTimeout(() => {
            this.popoverElement && this.popoverElement.hidePopover()
            this.copied = false
          }, 1000)
        }
      })
    }
  }
}

import { bind } from '../utils'

export function inputCopyable() {
  return {
    copied: false,
    timeout: null,

    init() {
      const input = this.$el
        ?.closest('[data-tallkit-field-control]')
        ?.querySelector('[data-tallkit-input]')

      if (!input) {
        return
      }

      bind(this.$el, {
        async ['@click']() {
          clearTimeout(this.timeout)

          this.copied = true
          this.popoverElement && this.popoverElement.showPopover()

          if (navigator.clipboard) {
            await navigator.clipboard.writeText(input.value)
            input.dispatchEvent(new Event('copied', { bubbles: false }))
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

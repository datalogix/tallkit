import { bind } from "../utils"

export function inputClearable() {
  return {
    init() {
      const input = this.$el.closest('[data-tallkit-input]').querySelector('input')

      if (!input) {
        return
      }

      bind(this.$el, {
        ['@click']() {
          input.value = ''
          input.dispatchEvent(new Event('input', { bubbles: false }))
          input.dispatchEvent(new Event('change', { bubbles: false }))
          input.focus()
        }
      })
    }
  }
}

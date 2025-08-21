import { bind } from "../utils"

export function inputClearable() {
  return {
    get input () {
      return this.$el.closest('[data-tallkit-input]').querySelector('input')
    },

    init() {
      if (!this.input) {
        return
      }

      bind(this.$el, {
        ['@click']() {
          this.input.value = ''
          this.input.dispatchEvent(new Event('input', { bubbles: false }))
          this.input.dispatchEvent(new Event('change', { bubbles: false }))
          this.input.focus()
        }
      })
    }
  }
}

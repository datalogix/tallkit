import { bind } from '../utils'

export function inputClearable() {
  return {
    get input () {
      return this.$el.closest('[data-tallkit-field-control]')?.querySelector('input')
    },

    init() {
      if (!this.input) {
        return
      }

      const button = this.$el
      button.style.display = this.input.value ? 'inline-flex ' : 'none'

      bind(this.input, {
        ['@input']() {
          button.style.display = this.$el.value ? 'inline-flex ' : 'none'
        }
      })

      bind(button, {
        ['@click']() {
          this.input.value = ''
          this.input.dispatchEvent(new Event('input', { bubbles: false }))
          this.input.dispatchEvent(new Event('change', { bubbles: false }))
          this.input.dispatchEvent(new Event('cleared', { bubbles: false }))
          this.input.focus()
        }
      })
    }
  }
}

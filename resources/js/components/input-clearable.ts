import { bind } from '../utils'

export function inputClearable() {
  return {
    init() {
      const input = this.$el
        ?.closest('[data-tallkit-field-control]')
        ?.querySelector('[data-tallkit-input]')

      if (!input) {
        return
      }

      const button = this.$el
      button.style.display = input.value ? 'inline-flex ' : 'none'

      bind(input, {
        ['@input']() {
          button.style.display = input.value ? 'inline-flex ' : 'none'
        }
      })

      bind(button, {
        ['@click']() {
          input.value = ''
          input.dispatchEvent(new Event('input', { bubbles: false }))
          input.dispatchEvent(new Event('change', { bubbles: false }))
          input.dispatchEvent(new Event('cleared', { bubbles: false }))
          input.focus()
        }
      })
    }
  }
}

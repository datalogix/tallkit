import { bind, findFieldInput, setFieldValue } from '../utils'

export function clearable() {
  return {
    init() {
      const button = this.$el

      if (this.clear) {
        bind(button, {
          ['@click']() {
            this.clear()
          }
        })
      }

      const input = findFieldInput(button)

      if (!input) {
        return
      }

      button.style.display = Boolean(input.value) ? 'block' : 'none'

      bind(input, {
        ['@input']() {
          button.style.display = Boolean(input.value) ? 'block' : 'none'
        }
      })

      bind(button, {
        ['@click']() {
          setFieldValue(input, '')
          input.dispatchEvent(new Event('cleared', { bubbles: true }))
          input.focus()
        }
      })
    }
  }
}

import { bind, findFieldInput, setFieldValue } from '../utils'

export function inputClearable() {
  return {
    hasValue: false,

    init() {
      const input = findFieldInput(this.$el)

      if (!input) {
        return
      }

      this.hasValue = Boolean(input.value)

      bind(input, {
        ['@input']() {
          this.hasValue = Boolean(input.value)
        }
      })

      bind(this.$el, {
        ['x-show']() {
          return this.hasValue
        },

        ['@click']() {
          setFieldValue(input, '')
          input.dispatchEvent(new Event('cleared', { bubbles: true }))
          input.focus()
        }
      })
    }
  }
}

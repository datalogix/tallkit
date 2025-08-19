import { bind } from '../utils'

export function checkboxGroup() {
  return {
    init() {
      const checkboxes = this.$el.querySelectorAll('[data-tallkit-checkbox]')
      const checkValue = (value) => checkboxes.forEach(
        checkbox => checkbox.checked = (Array.isArray(value) ? value : [value]).includes(checkbox.value)
      )

      checkValue(this.value)

      bind(checkboxes, {
        ['@change']() {
          this.$root.value = Array.from(checkboxes).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value)
          this.$root.dispatchEvent(new Event('input', { bubbles: true }))
          this.$root.dispatchEvent(new Event('change', { bubbles: true }))
        },
      })
    }
  }
}

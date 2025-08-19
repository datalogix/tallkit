export function radioGroup() {
  return {
    init() {
      const radios = this.$el.querySelectorAll('[data-tallkit-radio]')
      const checkValue = (value) => radios.forEach(
        radio => radio.checked = (Array.isArray(value) ? value : [value]).includes(radio.value)
      )

      checkValue(this.value)
      this.$watch('value', checkValue)
    }
  }
}

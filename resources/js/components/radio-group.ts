export function radioGroup() {
  return {
    get radios () {
      return this.$el.querySelectorAll('[data-tallkit-radio]')
    },

    init() {
      const checkValue = (value) => this.radios.forEach(
        radio => radio.checked = (Array.isArray(value) ? value : [value]).includes(radio.value)
      )

      checkValue(this.value)
      this.$watch('value', checkValue)
    }
  }
}

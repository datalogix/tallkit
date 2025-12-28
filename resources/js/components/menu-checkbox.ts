import { bind } from '../utils'

export function menuCheckbox(checked?: boolean) {
  return {
    checked,

    get isControlled() {
      return this.value !== undefined
    },

    get isArray() {
      return Array.isArray(this.value)
    },

    get isChecked() {
      if (!this.isControlled) {
        return this.checked
      }

      if (this.isArray) {
        return this.value.includes(this.$root.value)
      }

      return this.value == this.$root.value
    },

    init() {
      bind(this.$el, {
        ['@click']: () => this.toggle(),
        [':data-checked']: () => this.isChecked,
        [':aria-checked']: () => this.isChecked
      })
    },

    toggle() {
      if (!this.isControlled) {
        this.checked = !this.checked
        return
      }

      if (this.isArray) {
        this.value = this.isChecked
          ? this.value.filter(v => v !== this.$root.value)
          : [...this.value, this.$root.value]
        return
      }

      this.value = this.$root.value
    },
  }
}

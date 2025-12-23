import { bind } from '../utils'

export function menuRadio(checked?: boolean) {
  return {
    checked,

    get isControlled() {
      return this.value !== undefined
    },

    get isChecked() {
      return this.isControlled
        ? this.value == this.$root.value
        : this.checked
    },

    init() {
      bind(this.$el, {
        ['@click']: () => this.toggle(),
        [':data-checked']: () => this.isChecked,
        [':aria-checked']: () => this.isChecked
      })
    },

    toggle() {
      if (this.isControlled) {
        this.value = this.$root.value
      } else {
        this.checked = !this.checked
      }
    },
  }
}

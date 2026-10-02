import { bind } from '../utils'

// No `value` of its own: it would hide the group's.
export function menuItem(checked, type) {
  return {
    checked,

    isControlled() {
      return this.menuGroup === true
    },

    isArray() {
      return type === 'checkbox' && Array.isArray(this.value)
    },

    isChecked() {
      if (!this.isControlled()) {
        return this.checked
      }

      if (this.isArray()) {
        return this.value.some((v) => v == this.$root.value)
      }

      return this.value == this.$root.value
    },

    init() {
      bind(this.$el, {
        ['@click']: () => this.toggle(),
        [':data-checked']: () => this.isChecked(),
        // Always set, "false" too: menuitemcheckbox and menuitemradio require it.
        [':aria-checked']: () => (this.isChecked() ? 'true' : 'false')
      })
    },

    toggle() {
      if (!this.isControlled()) {
        this.checked = !this.checked
        return
      }

      if (this.isArray()) {
        this.value = this.isChecked()
          ? this.value.filter((v) => v != this.$root.value)
          : [...this.value, this.$root.value]
        return
      }

      if (type === 'radio') {
        this.value = this.$root.value
        return
      }

      this.value = this.isChecked() ? null : this.$root.value
    },
  }
}

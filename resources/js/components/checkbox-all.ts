import { bind } from '../utils'

export function checkboxAll({ group = '' } = {}) {
  return {
    all: null,
    checkboxes: [],

    init() {
      this.all = this.$root.querySelector('[data-tallkit-checkbox]')
      this.checkboxes = Array.from(document.querySelectorAll(`[data-checkbox-group="${group}"]`))

      bind(this.all, {
        ['@change']: () => this.toggleAll()
      })

      document.addEventListener('change', (event) => {
        const checkbox = event.target

        if (
          checkbox === this.all ||
          !checkbox.matches(`[data-checkbox-group="${group}"]`)
        ) {
          return
        }

        this.updateState()
      })

      this.updateState()
    },

    toggleAll() {
      this.checkboxes.forEach((checkbox) => {
        checkbox.checked = this.all?.checked

        this.$nextTick(() => {
          checkbox.dispatchEvent(new Event('input', { bubbles: true }))
          checkbox.dispatchEvent(new Event('change', { bubbles: true }))
        })
      })

      this.updateState()
    },

    updateState() {
      if (! this.all) return

      const total = this.checkboxes.length
      const checked = this.checkboxes.filter(cb => cb.checked).length

      this.all.checked = total > 0 && checked === total
      this.all.indeterminate = checked > 0 && checked < total
    }
  }
}

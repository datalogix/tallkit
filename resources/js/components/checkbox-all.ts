import { dataKey, bind } from '../utils'

export function checkboxAll({ group = '' } = {}) {
  return {
    all: null,

    get checkboxes() {
      return Array.from(document.querySelectorAll(dataKey('checkbox-group', group)))
    },

    init() {
      this.all = this.$root.querySelector(dataKey('checkbox'))

      bind(this.all, {
        ['@change']: () => this.toggleAll()
      })

      bind(this.checkboxes, {
        ['@change']: () => this.updateState()
      })
    },

    toggleAll() {
      this.checkboxes.forEach((checkbox) => {
        checkbox.checked = !!this.all?.checked
      })
    },

    updateState() {
      if (! this.all) return

      const checkboxes = this.checkboxes
      const total = checkboxes.length
      const checked = checkboxes.filter(cb => cb.checked).length

      this.all.checked = total > 0 && checked === total
      this.all.indeterminate = checked > 0 && checked < total
    }
  }
}

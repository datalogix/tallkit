import { dataKey, bind, allChecked } from '../utils'

export function groupAll(type: 'checkbox' | 'toggle', group: string) {
  return {
    all: null,

    get items() {
      return Array.from(document.querySelectorAll(dataKey(`${type}-group`, group)))
    },

    init() {
      this.all = this.$root.querySelector(dataKey(type))

      bind(this.all, {
        ['@change']: () => this.toggleAllItems()
      })

      bind(this.items, {
        ['@change']: () => this.updateState()
      })
    },

    toggleAllItems() {
      this.items.forEach((item) => {
        item.checked = !!this.all?.checked
        item.dispatchEvent(new Event('change', { bubbles: true }))
      })
    },

    updateState() {
      if (! this.all) return

      const items = this.items

      this.all.checked = allChecked(items, item => item.checked)

      if (type === 'checkbox') {
        const checkedCount = items.filter(item => item.checked).length
        this.all.indeterminate = checkedCount > 0 && checkedCount < items.length
      }
    }
  }
}

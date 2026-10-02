import { dataSelector, queryData, allChecked, onLivewireCommit } from '../utils'

export function checkAll(type, group) {
  return {
    all: null,
    _toggling: false,
    _onChange: null,
    _stopCommits: null,

    items() {
      const selector = group
        ? dataSelector(`${type}-group`, group)
        : `${dataSelector(type)}:not(${dataSelector(`${type}-group`)})`

      const scope = group
        ? document
        : (this.all?.closest('form') ?? this.all?.closest('[wire\\:id]') ?? document)

      return Array.from(scope.querySelectorAll(selector))
        .filter((item) => item !== this.all && !item.disabled
          && !item.closest('[x-data^="checkboxAll"], [x-data^="switchAll"]'))
    },

    init() {
      this.all = queryData(this.$root, type)

      if (!this.all) return

      this._onChange = (event) => {
        if (event.target === this.all) {
          this.toggleAllItems()
        } else if (!this._toggling && this.items().includes(event.target)) {
          this.updateState()
        }
      }

      document.addEventListener('change', this._onChange)

      this._stopCommits = onLivewireCommit(({ succeed }) => {
        succeed(() => this.$nextTick(() => this.updateState()))
      })

      this.updateState()
    },

    destroy() {
      document.removeEventListener('change', this._onChange)
      this._stopCommits?.()
    },

    toggleAllItems() {
      const checked = !!this.all?.checked

      this._toggling = true

      try {
        this.items().forEach((item) => {
          if (item.checked === checked) return

          item.checked = checked
          item.dispatchEvent(new Event('change', { bubbles: true }))
        })
      } finally {
        this._toggling = false
      }

      this.updateState()
    },

    updateState() {
      if (!this.all) return

      const items = this.items()

      this.all.checked = allChecked(items, (item) => item.checked)

      if (type === 'checkbox') {
        const checkedCount = items.filter((item) => item.checked).length
        this.all.indeterminate = checkedCount > 0 && checkedCount < items.length
      }
    }
  }
}

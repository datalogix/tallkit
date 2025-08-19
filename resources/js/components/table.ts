import { bind } from "../utils"

export function table() {
  return {
    processedRows: new Set(),
    rows: [],
    selected: [],
    selectedIds: [],
    selectAllChecked: false,

    init() {
      this.update()
      const observer = new MutationObserver(() => this.update())
      observer.observe(this.$el.querySelector('tbody'), { childList: true, subtree: true })
    },

    update() {
      this.rows = Array.from(this.$el.querySelectorAll('tbody>tr[role="row"]')).map(tr => {
        const selection = tr.querySelector('[role=row-selection]')
        const expanded = tr.querySelectorAll('[role=row-expanded]')

        const row = {
          el: tr,
          id: tr.dataset.id,
          selection,
          expanded,
        }

        if (!this.processedRows.has(row.id)) {
          this.processedRows.add(row.id)

          bind(selection, {
            ['@click']() {
              this._updateRowState(row)
              this._syncSelect()
            }
          })

          bind(expanded, {
            ['@click']() {
              row.el.dataset.expanded = row.el.dataset.expanded === 'open' ? 'close' : 'open'
            }
          })
        }

        return row
      })

      this.rows.forEach(row => {
        if (row.selection) {
          row.selection.checked = this.selectedIds.includes(row.id)
        }

        if (this.selectAllChecked) {
          row.selection.checked = true
        }

        this._updateRowState(row)
      })

      this._syncSelect()
    },

    toggleAll() {
      this.rows.forEach(row => {
        if (!row.selection) return
        row.selection.checked = this.selectAllChecked
        this._updateRowState(row)
      })
      this._syncSelect()
    },

    _updateRowState(row) {
      if (row.selection) {
        row.el.dataset.state = row.selection.checked ? 'checked' : 'unchecked'
      }

      if (row.expanded && !row.el.dataset.expanded) {
        row.el.dataset.expanded = 'close'
      }
    },

    _syncSelect() {
      this.selected = this.rows.filter(row => row.selection?.checked)
      this.selectedIds = this.selected.map(row => row.id)
      this.selectAllChecked = this.rows.length && this.rows.every(row => row.selection?.checked)
    },
  }
}

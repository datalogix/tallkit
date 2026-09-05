import { bind, setFieldChecked, allChecked } from '../utils'

export function table() {
  return {
    boundElements: new WeakSet(),
    rows: [],
    selected: [],
    selectedIds: [],
    selectAllChecked: false,
    observer: null,

    init() {
      this.resetSelection()

      const tbody = this.$el.querySelector('table > tbody')
      if (!tbody) return

      this.observer = new MutationObserver(() => this.update())
      this.observer.observe(tbody, { childList: true, subtree: true })
    },

    destroy() {
      this.observer?.disconnect()
    },

    update() {
      const tbody = this.$el.querySelector('table > tbody')
      const trs = tbody ? Array.from(tbody.querySelectorAll(':scope > tr[role="row"]')) : []

      this.rows = trs.map(tr => {
        const selection = tr.querySelector('[data-role=row-selection]')
        const expanded = tr.querySelectorAll('[data-role=row-expanded]')

        const row = {
          el: tr,
          id: tr.dataset.id,
          selection,
          expanded,
        }

        if (selection && !this.boundElements.has(selection)) {
          this.boundElements.add(selection)

          bind(selection, {
            ['@click']() {
              this._updateRowState(row)
              this._syncSelect()
            }
          })
        }

        const unboundExpanded = Array.from(expanded).filter((el) => !this.boundElements.has(el))

        if (unboundExpanded.length) {
          unboundExpanded.forEach((el) => this.boundElements.add(el))

          bind(unboundExpanded, {
            ['@click']() {
              row.el.dataset.expanded = row.el.dataset.expanded === 'open' ? 'close' : 'open'
            }
          })
        }

        return row
      })

      this.rows.forEach((row) => {
        if (row.selection) {
          setFieldChecked(row.selection, this.selectAllChecked || this.selectedIds.includes(row.id))
        }

        this._updateRowState(row)
      })

      this._syncSelect()
    },

    toggleAll() {
      this.rows.forEach((row) => {
        if (!row.selection) return
        setFieldChecked(row.selection, this.selectAllChecked)
        this._updateRowState(row)
      })
      this._syncSelect()
    },

    resetSelection() {
      this.selected = []
      this.selectedIds = []
      this.selectAllChecked = false
      this.update()
    },

    _updateRowState(row) {
      if (row.selection) {
        row.el.dataset.state = row.selection.checked ? 'checked' : 'unchecked'
      }

      if (row.expanded.length && !row.el.dataset.expanded) {
        row.el.dataset.expanded = 'close'
      }
    },

    _syncSelect() {
      this.selected = this.rows.filter((row) => row.selection?.checked)
      this.selectedIds = this.selected.map((row) => row.id)
      this.selectAllChecked = allChecked(this.rows, (row) => !!row.selection?.checked)
    },
  };
}

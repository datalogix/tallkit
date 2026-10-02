import { bind, setFieldValue } from '../utils'
import { popover } from './popover'
import { listbox } from './listbox'

export function autocomplete(options = {}) {
  const _popover = popover({ mode: 'manual', position: 'bottom', align: 'start', matchTriggerWidth: true })
  const _listbox = listbox({ hideEmpty: true, clearOnSelect: false, autoHighlight: false, tabSelects: false, ...options })

  return {
    ..._popover,
    ..._listbox,

    _chosen: false,

    resolvePopoverTrigger() {
      return this.input ?? _popover.resolvePopoverTrigger.call(this)
    },

    // Both halves: spread one after the other, the listbox's destroy() replaces the popover's.
    destroy() {
      _popover.destroy.call(this)
      _listbox.destroy.call(this)
    },

    init() {
      _popover.init.call(this)
      _listbox.init.call(this)

      this.trigger = this.input

      bind(this.input, {
        ['@keydown']() {
          this._chosen = false
        },

        ['@blur']() {
          this.close()
        },

        ['@keydown.escape.prevent']() {
          this.close()
        },
      })

      bind(this.$root, {
        ['@selected']({
          detail
        }) {
          setFieldValue(this.input, detail.item.title)

          this.debouncedSearch?.cancel()
          this._chosen = true
          this.close()
        }
      })
    },

    search() {
      _listbox.search.call(this)

      if (this._chosen) return this.close()

      const typing = document.activeElement === this.input && !this.input.disabled && !this.input.readOnly

      if (this.filteredItems.length && typing) {
        this.open()
      } else {
        this.close()
      }
    },

    open() {
      _popover.open.call(this, false)
    },

    close() {
      _popover.close.call(this)
      this.clear()
    },
  }
}

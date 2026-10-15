import { announce, bind, emit, setFieldValue } from '../utils'
import { popover } from './popover'
import { listbox } from './listbox'

export function autocomplete({ texts = {}, ...options } = {}) {
  const _popover = popover({ mode: 'manual', position: 'bottom', align: 'start', matchTriggerWidth: true })
  const _listbox = listbox({ hideEmpty: true, clearOnSelect: false, autoHighlight: false, tabSelects: false, ...options })

  return {
    ..._popover,
    ..._listbox,

    _chosen: false,
    _typed: false,

    resolvePopoverTrigger() {
      return this.input ?? _popover.resolvePopoverTrigger.call(this)
    },

    destroy() {
      _popover.destroy.call(this)
      _listbox.destroy.call(this)
    },

    init() {
      _popover.init.call(this)
      _listbox.init.call(this)

      this.trigger = this.input

      bind(this.input, {
        ['@input'](event) {
          if (!event.isTrusted) return

          this._chosen = false
          this._typed = true
        },

        ['@blur']() {
          this.close()
        },

        ['@keydown.escape.prevent']() {
          this._chosen = true
          this.close()
        },
      })

      bind(this.$root, {
        ['@selected']({
          detail
        }) {
          if (!options.clearOnSelect) setFieldValue(this.input, detail.item.title)

          this.debouncedSearch?.cancel()
          this._chosen = true
          this.close()
        }
      })

      bind(this.$root, {
        ['@mousedown'](event) {
          if (this.popoverElement?.contains(event.target)) event.preventDefault()
        },
      })

      bind(this.$root, Object.fromEntries(['ready', 'searched', 'filtered', 'highlighted', 'selected', 'opened', 'closed'].map((name) => [
        `@${name}`, (event) => emit(this.input, name, event.detail),
      ])))
    },

    search() {
      _listbox.search.call(this)

      if (this._chosen) return this.close()

      if (this._typed && this.input.value.trim()) this.announceCount()
      this._typed = false

      const typing = document.activeElement === this.input && !this.input.disabled && !this.input.readOnly

      if (this.filteredItems.length && typing) {
        this.open()
      } else {
        this.close()
      }
    },

    announceCount() {
      const count = this.filteredItems.length
      const text = count === 0 ? texts.none : (count === 1 ? texts.one : texts.other)

      announce(text?.replace(':count', count))
    },

    prev() {
      if (!this.isOpened()) return this.reopen()

      _listbox.prev.call(this)
    },

    next() {
      if (!this.isOpened()) return this.reopen()

      _listbox.next.call(this)
    },

    reopen() {
      if (this.filteredItems.length) this.open()
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

import { bind, setFieldValue } from '../utils'
import { popover } from './popover'
import { listbox } from './listbox'

export function autocomplete(options = {}) {
  const _popover = popover({ mode: 'manual', position: 'bottom', align: 'start' })
  const _listbox = listbox({ hideEmpty: true, clearOnSelect: false, ...options })

  return {
    ..._popover,
    ..._listbox,

    init() {
      _popover.init.call(this)
      _listbox.init.call(this)

      bind(this.input, {
        ['@blur']() {
          this.close()
        },

        ['@keydown.esc.prevent']() {
          this.close()
        },
      })

      bind(this.$root, {
        ['@listbox-item-selected']({ detail }) {
          setFieldValue(this.input, detail.item.title)
          this.close()
        }
      })
    },

    search() {
      _listbox.search.call(this)

      if (this.filteredItems.length) {
        this.open()
      } else {
        this.close()
      }
    },

    open() {
      this.popoverElement.style.width = `${this.input.offsetWidth}px`
      _popover.open.call(this, false)
    },

    close() {
      _popover.close.call(this)
      this.clear()
    },
  }
}

import { bind } from '../utils'
import { popover } from './popover'
import { listbox } from './listbox'

export function combobox({ value = null, multiple = false } = {}) {
  const _popover = popover({ mode: 'manual', position: 'bottom', align: 'start' })
  const _listbox = listbox({ hideEmpty: false, clearOnSelect: !multiple })

  return {
    ..._popover,
    ..._listbox,

    value: value ?? (multiple ? [] : null),
    combobox: null,

    get selectedLabel() {
      if (multiple || this.value == null) return null

      const item = this.items.find(i => String(this.getElementValue(i.el)) === String(this.value))

      return item ? item.el.querySelector('[data-item-content]')?.textContent?.trim() : null
    },

    get selectedCount() {
      return this.items.filter(item => this.isSelected(this.getElementValue(item.el))).length
    },

    init() {
      _popover.init.call(this)
      _listbox.init.call(this)

      this.combobox = this.$root.querySelector('[data-tallkit-combobox]')

      bind(this.combobox, {
        ['@click']() {
          this.combobox.focus()
          this.toggle()
        },

        ['@keydown.enter.prevent']() {
          if (this.opened) return
          this.open()
        },

        ['@keydown.space.prevent']() {
          if (this.opened) return
          this.open()
        },

        ['@keydown.arrow-up.prevent']: () => {
          if (this.opened) return
          this.open()
        },

        ['@keydown.arrow-down.prevent']: () => {
          if (this.opened) return
          this.open()
        },
      })

      bind([this.combobox, this.popoverElement, this.input, this.list], {
        ['@keydown.escape.prevent']() {
          this.closeAndFocus()
        },
      })

      bind(this.$root, {
        ['@click.outside']() {
          this.close()
        },

        ['@listbox-item-selected']({ detail }) {
          this.pick(this.getElementValue(detail.button))
        },

        ['@listbox-items-changed']() {
          this.syncChecked()
        },
      })

      this.$watch('value', () => this.syncChecked())
      this.$nextTick(() => this.syncChecked())
    },

    open() {
      this.popoverElement.style.width = `${this.combobox.offsetWidth}px`
      _popover.open.call(this, false)

      const target = multiple ? this.value.at(-1) : this.value
      const index = this.filteredItems.findIndex(item => String(this.getElementValue(item.el)) === String(target))
      this.index = index === -1 ? null : index

      requestAnimationFrame(() => {
        this.list?.focus()
      })
    },

    close() {
      _popover.close.call(this)
      this.clear()
    },

    closeAndFocus() {
      this.close()
      this.combobox.focus()
    },

    isSelected(v) {
      if (!multiple) {
        return String(this.value ?? '') === String(v)
      }

      if (!Array.isArray(this.value) && this.value != null) {
        this.value = [this.value]
      }

      return this.value.map(String).includes(String(v))
    },

    pick(v) {
      if (multiple) {
        this.value = this.isSelected(v)
          ? this.value.filter(x => String(x) !== String(v))
          : [...this.value, v]
      } else {
        this.value = this.isSelected(v) ? null : v
        this.closeAndFocus()
      }
    },

    remove(v) {
      if (!multiple) return
      this.value = this.value.filter(x => String(x) !== String(v))
    },

    clearValue() {
      this.value = multiple ? [] : null
    },

    syncChecked() {
      this.items.forEach(item => {
        const selected = this.isSelected(this.getElementValue(item.el))
        const mark = item.el.querySelector('[data-tallkit-checkmark]')
        if (mark) mark.classList.toggle('invisible', !selected)
        item.li.setAttribute('aria-selected', String(selected))
      })
    },

    getElementValue(el) {
      return el.getAttribute('value') ?? el.textContent?.trim() ?? null
    }
  }
}

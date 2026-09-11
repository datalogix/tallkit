import { dataKey, bind, setFieldValue } from '../utils'
import { popover } from './popover'
import { listbox } from './listbox'
import { bindableField } from '../mixins/bindable-field'

export function combobox(
  {
    value = null,
    multiple = false,
    type = null
  } = {}
) {
  const isInputTrigger = type === 'input'
  const _popover = popover({ mode: 'manual', position: 'bottom', align: 'start', matchTriggerWidth: true })
  const _listbox = listbox({ hideEmpty: false, clearOnSelect: !multiple })
  const _bindableField = bindableField({
    key: 'combobox-field',
    serialize() { return this.valueString() },
    deserialize(raw) { return multiple ? (raw ? raw.split(',').filter(Boolean) : []) : raw },
  })

  return {
    ..._popover,
    ..._listbox,
    ..._bindableField,

    value: value ?? (multiple ? [] : null),
    combobox: null,

    selectedLabel() {
      if (multiple || this.value == null) return null

      const item = this.items.find((i) => String(this.getElementValue(i.el)) === String(this.value))

      return item ? item.el.querySelector('[data-item-content]')?.textContent?.trim() : null
    },

    selectedCount() {
      return this.items.filter((item) => this.isSelected(this.getElementValue(item.el))).length;
    },

    selectedOrder(v) {
      return this.value.map(String).indexOf(String(v))
    },

    isDisabled() {
      return this.combobox.hasAttribute('disabled')
    },

    valueString() {
      return multiple ? (this.value ?? []).join(',') : (this.value ?? null)
    },

    syncInputDisplay() {
      if (!isInputTrigger) return

      setFieldValue(this.input, multiple ? '' : (this.selectedLabel() ?? ''))
    },

    init() {
      _popover.init.call(this)
      _listbox.init.call(this)

      this.combobox = isInputTrigger ? this.input : this.$root.querySelector(dataKey('combobox'))

      _bindableField.init.call(this)

      if (isInputTrigger) {
        bind(this.input, {
          ['@focus']() {
            if (this.isDisabled()) return
            this.open()
          },

          ['@blur']() {
            this.syncInputDisplay()
          },

          ['@keydown.backspace']() {
            if (this.isDisabled()) return
            if (!multiple || this.input.value || this.value.length === 0) return
            this.remove(this.value.at(-1))
          },
        })

        this.syncInputDisplay()
      } else {
        bind(this.combobox, {
          ['@click']() {
            if (this.isDisabled()) return
            this.combobox.focus()
            this.toggle()
          },

          ['@keydown.enter.prevent']() {
            if (this.isDisabled()) return
            if (!this.opened) return this.open()
            this.select(this.index)
          },

          ['@keydown.space.prevent']() {
            if (this.isDisabled()) return
            if (!this.opened) return this.open()
            this.select(this.index)
          },

          ['@keydown.arrow-up.prevent']() {
            if (this.isDisabled()) return
            if (!this.opened) return this.open()
            this.lastInteraction = 'keyboard'
            this.prev()
          },

          ['@keydown.arrow-down.prevent']() {
            if (this.isDisabled()) return
            if (!this.opened) return this.open()
            this.lastInteraction = 'keyboard'
            this.next()
          },
        })
      }

      bind([this.combobox, this.popoverElement, this.input, this.list], {
        ['@keydown.escape.prevent']() {
          this.closeAndFocus()
        },
      })

      bind(this.$root, {
        ['@click.outside']() {
          this.close()
        },

        ['@listbox-item-selected']({
          detail
        }) {
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
      if (this.isDisabled()) return

      _popover.open.call(this, false)

      const target = multiple ? this.value.at(-1) : this.value
      const index = this.filteredItems.findIndex((item) => String(this.getElementValue(item.el)) === String(target))
      this.index = index === -1 ? null : index

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          this.input?.focus()
        })
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
          ? this.value.filter((x) => String(x) !== String(v))
          : [...this.value, v]

        if (isInputTrigger) {
          this.syncInputDisplay()
          this.search()
        }
      } else {
        this.value = this.isSelected(v) ? null : v
        if (isInputTrigger) this.syncInputDisplay()
        this.closeAndFocus()
      }
    },

    remove(v) {
      if (!multiple) return
      this.value = this.value.filter((x) => String(x) !== String(v))
    },

    clearValue() {
      if (this.isDisabled()) return

      this.value = multiple ? [] : null
      this.syncInputDisplay()
    },

    syncChecked() {
      this.items.forEach((item) => {
        const selected = this.isSelected(this.getElementValue(item.el))
        const mark = item.el.querySelector(dataKey('checkmark'))
        if (mark) mark.classList.toggle('invisible', !selected)
        item.li.setAttribute('aria-selected', String(selected))
      })
    },

    getElementValue(el) {
      return el.getAttribute('value') ?? el.textContent?.trim() ?? null
    }
  };
}

import { queryData, bind, setFieldValue, focusOnLabelClick } from '../utils'
import { popover } from './popover'
import { listbox } from './listbox'
import { bindableField } from '../mixins/bindable-field'

export function combobox(
  {
    value = null,
    multiple = false,
    trigger = null
  } = {}
) {
  const isInputTrigger = trigger === 'input'
  const _popover = popover({ mode: 'manual', position: 'bottom', align: 'start', matchTriggerWidth: true })
  const _listbox = listbox({ hideEmpty: false, clearOnSelect: !multiple })
  const _bindableField = bindableField({
    key: 'combobox-field',
    serialize() { return this.valueString() },
    deserialize(raw) {
      if (!multiple) return raw
      if (Array.isArray(raw)) return [...raw]

      return raw ? String(raw).split(',').filter(Boolean) : []
    },
    toWire: multiple ? function () { return [...(this.value ?? [])] } : null,
  })

  return {
    ..._popover,
    ..._listbox,
    ..._bindableField,

    value: value ?? (multiple ? [] : null),
    combobox: null,
    _stopLabelFocus: null,

    selectedLabel() {
      if (multiple || this.value == null) return null

      const item = this.items.find((i) => String(this.getElementValue(i.el)) === String(this.value))

      return item ? item.el.querySelector('[data-item-content]')?.textContent?.trim() : null
    },

    selectedCount() {
      return this.items.filter((item) => this.isSelected(this.getElementValue(item.el))).length
    },

    selectedValues() {
      return multiple && Array.isArray(this.value) ? this.value : []
    },

    optionLabel(v) {
      const item = this.items.find((i) => String(this.getElementValue(i.el)) === String(v))
      const el = item?.el ?? this.$root.querySelector(`[role=option] [value="${CSS.escape(String(v))}"]`)

      return el?.querySelector('[data-item-content]')?.textContent?.trim() || String(v)
    },

    isDisabled() {
      return this.combobox.hasAttribute('disabled')
    },

    isReadonly() {
      return this.combobox.getAttribute('aria-readonly') === 'true'
    },

    valueString() {
      return multiple ? (this.value ?? []).join(',') : (this.value ?? null)
    },

    syncInputDisplay() {
      if (!isInputTrigger) return

      setFieldValue(this.input, multiple ? '' : (this.selectedLabel() ?? ''))
    },

    // Both halves: spread one after the other, the listbox's destroy() replaces the popover's.
    destroy() {
      _popover.destroy.call(this)
      _listbox.destroy.call(this)
      this._stopLabelFocus?.()
    },

    init() {
      _popover.init.call(this)
      _listbox.init.call(this)

      this.combobox = isInputTrigger ? this.input : queryData(this.$root, 'combobox')

      _bindableField.init.call(this)

      if (this.combobox && !('labels' in this.combobox)) {
        this._stopLabelFocus = focusOnLabelClick(this.combobox, () => this.isDisabled() || this.combobox.focus())
      }

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
            if (this.isDisabled() || this.isReadonly()) return
            if (!multiple || this.input.value || this.value.length === 0) return
            this.remove(this.value.at(-1))
          },
        })

        this.syncInputDisplay()
      } else {
        bind(this.combobox, {
          ['@keydown.backspace.prevent']() {
            this.clearFromKeyboard()
          },

          ['@keydown.delete.prevent']() {
            this.clearFromKeyboard()
          },

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

      bind(this.input, {
        ['@keydown.enter.prevent']() {},
      })

      bind(this.$root, {
        ['@click.outside']() {
          this.close()
        },

        ['@focusout'](event) {
          const to = event.relatedTarget

          if (to instanceof Element && !this.$root.contains(to) && this.isOpened()) this.close()
        },

        ['@selected']({
          detail
        }) {
          this.pick(this.getElementValue(detail.button))
        },

        ['@filtered']() {
          this.syncChecked()
        },
      })

      this.$watch('value', () => this.syncChecked())
      this.$nextTick(() => this.syncChecked())
    },

    open() {
      if (this.isDisabled()) return

      _popover.open.call(this, false)

      const highlightChosen = () => {
        const target = multiple ? this.value.at(-1) : this.value
        const index = this.filteredItems.findIndex((item) => String(this.getElementValue(item.el)) === String(target))
        this.index = index === -1 ? null : index
      }

      highlightChosen()

      // Once the popover is on screen: a hidden element can't take focus. Its search clears the highlight, so set after
      // it.
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          this.input?.focus()
          if (this.input) highlightChosen()
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
        this.value = v
        if (isInputTrigger) this.syncInputDisplay()
        this.closeAndFocus()
      }

      this.dispatchPicked(this.value)
    },

    remove(v) {
      if (!multiple || this.isReadonly()) return
      this.value = this.value.filter((x) => String(x) !== String(v))
      this.dispatchPicked(this.value)
    },

    clearFromKeyboard() {
      if (this.isDisabled() || this.isReadonly() || this.opened) return

      if (multiple) {
        if (this.value.length) this.remove(this.value.at(-1))
      } else {
        this.clearValue()
      }
    },

    clearValue() {
      if (this.isDisabled() || this.isReadonly()) return

      this.value = multiple ? [] : null
      this.syncInputDisplay()
    },

    syncChecked() {
      this.items.forEach((item) => {
        const selected = this.isSelected(this.getElementValue(item.el))
        const mark = queryData(item.el, 'checkmark')
        if (mark) mark.classList.toggle('invisible', !selected)
        item.li.setAttribute('aria-selected', String(selected))
      })
    },

    getElementValue(el) {
      return el.getAttribute('value') ?? el.textContent?.trim() ?? null
    }
  }
}

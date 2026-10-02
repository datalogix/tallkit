import { bind, setFieldValue, hasLivewire, focusOnLabelClick, emit } from '../utils'
import { bindableField } from '../mixins/bindable-field'

export function otp(submit) {
  const _bindableField = bindableField({
    key: 'otp-field',
    deserialize(raw) { return raw || '' },
  })

  return {
    ..._bindableField,

    value: '',
    inputs: [],
    _syncing: false,
    _submitted: null,
    _stopLabelFocus: null,

    init() {
      this.inputs = Array.from(this.$root.querySelectorAll('input[data-charset]'))

      _bindableField.init.call(this)

      // Only the user completing the code submits it: a prefilled code must not send itself.
      this.$nextTick(() => {
        this.syncFromModel()
        this.updateModel(false)
      })

      this.$watch('value', (val) => {
        if (val === this.boxesValue()) return

        this.syncFromModel(val)
        this.updateModel(false)
      })

      this.inputs.forEach((input, index) => {
        bind(input, this.bindings(input, index, this.inputs))
      })

      this._stopLabelFocus = focusOnLabelClick(this.$root, () => this.inputs[0]?.focus())
    },

    destroy() {
      this._stopLabelFocus?.()
    },

    emitOnBox(name, detail, box = this.inputs[0]) {
      emit(box, name, detail)
    },

    bindings(input, index, inputs) {
      return {
        ['@focus']: (e) => this.handleFocus(input, index, inputs, e),
        ['@blur']: () => this.emitOnBox('blurred', { input, index }, input),
        ['@paste.prevent']: (e) => this.handlePaste(e, index, inputs),
        ['@input']: () => this.handleInput(input, index, inputs),
        ['@keydown']: (e) => this.handleKeydown(e, input, index, inputs),
        ['@keydown.arrow-left.prevent']: () => inputs[index - 1]?.select(),
        ['@keydown.arrow-right.prevent']: () => inputs[index + 1]?.select(),
        ['@keydown.backspace.prevent']: () => this.handleBackspace(input, index, inputs),
      }
    },

    handleFocus(input, index, inputs, event = null) {
      // From another of its boxes, focus stays: sent back to the first empty one, Tab would never leave (a keyboard
      // trap).
      if (input.value || inputs.includes(event?.relatedTarget)) {
        input.select()
        this.emitOnBox('focused', { input, index }, input)
        return
      }

      const firstEmpty = inputs.find(i => !i.value)
      firstEmpty?.select()

      this.emitOnBox('focused', {
        input: firstEmpty || input,
        index: inputs.indexOf(firstEmpty || input),
      }, firstEmpty || input)
    },

    handlePaste(e, index, inputs) {
      const pasted = e.clipboardData?.getData('text') ?? ''

      this._syncing = true
      try {
        spreadValue(pasted, index, inputs)
      } finally {
        this._syncing = false
      }

      this.updateModel()

      this.emitOnBox('pasted', { pasted, index })
    },

    handleInput(input, index, inputs) {
      if (this._syncing) return

      const charset = input.dataset.charset
      const filtered = filterValue(input.value, charset)

      if (filtered.length > 1) {
        spreadValue(filtered, index, inputs)
      } else {
        input.value = filtered
        if (filtered) inputs[index + 1]?.focus()
      }

      this.updateModel()
    },

    handleKeydown(e, input, _index, _inputs) {
      if (e.ctrlKey || e.metaKey || e.altKey) return

      const charset = input.dataset.charset

      if (!isValidKey(e.key, charset)) {
        e.preventDefault()
      }
    },

    handleBackspace(input, index, inputs) {
      if (input.value) {
        this._syncing = true
        setFieldValue(input, '')
        this._syncing = false
      } else {
        inputs[index - 1]?.select()
      }

      this.updateModel()
    },

    syncFromModel(val) {
      val ??= this.value
      // String(null) would be "null".
      const chars = String(val ?? '').padEnd(this.inputs.length).split('')

      this._syncing = true
      try {
        this.inputs.forEach((input, i) => {
          const charset = input.dataset.charset
          setFieldValue(input, filterValue(chars[i] ?? '', charset))
        })
      } finally {
        this._syncing = false
      }
    },

    boxesValue() {
      return this.inputs.map((i) => i.value || '').join('')
    },

    updateModel(byUser = true) {
      const values = this.inputs.map((i) => i.value || '')
      this.value = values.join('')

      const filled = values.filter(Boolean).length

      // No events: a field set by a script fires none.
      if (!byUser) {
        if (filled < this.inputs.length) this._submitted = null

        return
      }

      this.emitOnBox('changed', { value: this.value })

      if (filled === this.inputs.length) {
        this.emitOnBox('completed', { value: this.value })

        if (this.value !== this._submitted) {
          this._submitted = this.value

          if (submit === 'auto') {
            this.$root.closest('form')?.requestSubmit()
          } else if (submit && hasLivewire()) {
            window.Livewire.dispatch(submit, this.value)
          }
        }
      } else {
        this._submitted = null

        this.emitOnBox('incomplete', { value: this.value })
      }

      if (filled === 0) {
        this.emitOnBox('cleared', {})
      }
    },
  }
}

function filterValue(value, charset = 'numeric') {
  const map = {
    numeric: /[0-9]/g,
    alpha: /[A-Z]/g,
    alphanumeric: /[A-Z0-9]/g,
  }

  return (value.toUpperCase().match(map[charset]) || []).join('')
}

function isValidKey(key, charset) {
  const control = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End', 'Enter', 'Escape']

  if (control.includes(key)) return true

  return filterValue(key, charset).length > 0
}

function spreadValue(value, start, inputs) {
  const chars = value.split('')
  let box = start

  for (const char of chars) {
    const input = inputs[box]
    if (!input) break

    const filtered = filterValue(char, input.dataset.charset)
    if (!filtered) continue

    setFieldValue(input, filtered)
    box++
  }

  inputs[Math.min(box, inputs.length - 1)]?.focus()
}

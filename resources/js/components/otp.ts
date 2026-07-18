import { bind } from '../utils'

type Mode = 'numeric' | 'alpha' | 'alphanumeric'

export function otp(submit?: string) {
  return {
    value: '',
    inputs: [],

    init() {
      this.inputs = Array.from(this.$root.querySelectorAll('input'))

      this.$nextTick(() => {
        this.syncFromModel()
        this.updateModel()
      })

      this.$watch('value', (val: string) => {
        this.syncFromModel(val)
        this.updateModel()
      })

      this.inputs.forEach((input, index) => {
        bind(input, this.bindings(input, index, this.inputs))
      })
    },

    bindings(input: HTMLInputElement, index: number, inputs: HTMLInputElement[]) {
      return {
        ['@focus']: () => this.handleFocus(input, index, inputs),
        ['@blur']: () => this.$dispatch('otp-blur', { input, index }),
        ['@paste']: (e: ClipboardEvent) => this.handlePaste(e, index, inputs),
        ['@input']: () => this.handleInput(input, index, inputs),
        ['@keydown']: (e: KeyboardEvent) => this.handleKeydown(e, input, index, inputs),
        ['@keydown.arrow-left.prevent']: () => inputs[index - 1]?.select(),
        ['@keydown.arrow-right.prevent']: () => inputs[index + 1]?.select(),
        ['@keydown.backspace.prevent']: () => this.handleBackspace(input, index, inputs),
      }
    },

    handleFocus(input: HTMLInputElement, index: number, inputs: HTMLInputElement[]) {
      if (input.value) {
        input.select()
        this.$dispatch('otp-focus', { input, index })
        return
      }

      const firstEmpty = inputs.find(i => !i.value)
      firstEmpty?.select()

      this.$dispatch('otp-focus', {
        input: firstEmpty || input,
        index: inputs.indexOf(firstEmpty || input),
      })
    },

    handlePaste(e: ClipboardEvent, index: number, inputs: HTMLInputElement[]) {
      const pasted = e.clipboardData?.getData('text') ?? ''
      spreadValue(pasted, index, inputs)
      this.updateModel()

      this.$dispatch('otp-paste', { pasted, index })
    },

    handleInput(input: HTMLInputElement, index: number, inputs: HTMLInputElement[]) {
      const mode = input.dataset.mode as Mode
      const filtered = filterValue(input.value, mode)

      if (filtered.length > 1) {
        spreadValue(filtered, index, inputs)
      } else {
        input.value = filtered
        if (filtered) inputs[index + 1]?.focus()
      }

      this.updateModel()
    },

    handleKeydown(e: KeyboardEvent, input: HTMLInputElement, index: number, inputs: HTMLInputElement[]) {
      if (e.ctrlKey || e.metaKey || e.altKey) return

      const mode = input.dataset.mode as Mode

      if (!isValidKey(e.key, mode)) {
        e.preventDefault()
      }
    },

    handleBackspace(input: HTMLInputElement, index: number, inputs: HTMLInputElement[]) {
      if (input.value) {
        input.value = ''
      } else {
        inputs[index - 1]?.select()
      }

      this.updateModel()
    },

    syncFromModel(val: string = this.value) {
      const chars = String(val).padEnd(this.inputs.length).split('')

      this.inputs.forEach((input, i) => {
        const mode = input.dataset.mode as Mode
        input.value = filterValue(chars[i] ?? '', mode)
      })
    },

    updateModel() {
      const values = this.inputs.map(i => i.value || '')
      this.value = values.join('')

      const filled = values.filter(Boolean).length

      this.$dispatch('otp-change', { value: this.value })

      if (filled === this.inputs.length) {
        this.$dispatch('otp-complete', { value: this.value })

        if (submit === 'auto') {
          this.$root.closest('form')?.requestSubmit()
        } else if (submit && window.Livewire) {
          window.Livewire.dispatch(submit, this.value)
        }
      } else {
        this.$dispatch('otp-incomplete', { value: this.value })
      }

      if (filled === 0) {
        this.$dispatch('otp-clear')
      }
    },
  }
}

function filterValue(value: string, mode: Mode = 'numeric') {
  const map = {
    numeric: /[0-9]/g,
    alpha: /[A-Z]/g,
    alphanumeric: /[A-Z0-9]/g,
  }

  return (value.toUpperCase().match(map[mode]) || []).join('')
}

function isValidKey(key: string, mode: Mode) {
  const control = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight']

  if (control.includes(key)) return true

  return filterValue(key, mode).length > 0
}

function spreadValue(value: string, start: number, inputs: HTMLInputElement[]) {
  const chars = value.split('')

  chars.forEach((char, i) => {
    const input = inputs[start + i]
    if (!input) return

    const mode = input.dataset.mode as Mode
    input.value = filterValue(char, mode)
  })

  const next = inputs[Math.min(start + chars.length, inputs.length - 1)]
  next?.focus()
}

import { bind } from '../utils'

export function otp(submit?: string) {
  return {
    value: '',

    get inputs () {
      return Array.from(
        this.$root.querySelectorAll('[data-tallkit-otp-input]')
      ) as HTMLInputElement[]
    },

    get length () {
      return this.inputs.length
    },

    init() {
      const inputs = this.inputs

      this.$nextTick(() => this.updateModel())
      this.$watch('value', (newVal: string) => syncInputs(inputs, newVal))

      inputs.forEach((input, index) => {
        bind(input, {
          ['@focus']() {
            input.select()

            this.$dispatch('otp-focus', { input, index })
          },

          ['@blur']() {
            this.$dispatch('otp-blur', { input, index })
          },

          ['@paste'](e) {
            const pasted = e.clipboardData.getData('text')

            this.$dispatch('otp-paste', { input, index, pasted })
          },

          ['@input']() {
            const value = filterValue(input.value, input.dataset.mode)

            if (value.length > 1) {
              spreadValue(value, index, inputs)
            } else {
              input.value = value

              if (value) {
                if (inputs[index + 1]) {
                  inputs[index + 1].focus()
                } else {
                  inputs.filter(input => !input.value).at(0)?.focus()
                }
              }
            }

            this.updateModel()
          },

          ['@keydown.arrow-left.prevent']: () => inputs[index - 1]?.focus(),
          ['@keydown.arrow-right.prevent']: () => inputs[index + 1]?.focus(),
          ['@keydown.backspace']: () => {
            if (!input.value && inputs[index - 1]) {
              inputs[index - 1].focus()
            }
          },
        })
      })

      syncInputs(inputs, this.value)
    },

    updateModel() {
      const old = this.value
      this.value = this.inputs.map(i => i.value || ' ').join('')
      const len = this.value.replace(/\s+/g, '').length

      if (old === this.value) {
        return
      }

      this.$dispatch('otp-change', { value: this.value })

      if (len === this.length) {
        this.$dispatch('otp-complete', { value: this.value })

        if (submit === 'auto') {
          this.$root.closest('form')?.dispatchEvent(new Event('submit'))
        }
      }

      if (len !== this.length) {
        this.$dispatch('otp-incomplete', { value: this.value })
      }

      if (len === 0) {
        this.$dispatch('otp-clear')
      }
    },
  }
}

function syncInputs(inputs: HTMLInputElement[], modelValue: string) {
  const chars = modelValue.padEnd(inputs.length).split('')

  inputs.forEach((input, i) => {
    input.value = filterValue(chars[i] ?? '', input.dataset.mode)
  })
}

function filterValue(value: string, mode?: string) {
  return (value.toLocaleUpperCase().match(
    mode === 'alpha' ? /[A-Z]/g : mode === 'alphanumeric' ? /[A-Z0-9]/g : /[0-9]/g
  ) || []).join('')
}

function spreadValue(value: string, startIndex: number, inputs: HTMLInputElement[]) {
  const chars = value.split('')

  chars.forEach((char, i) => {
    const target = inputs[startIndex + i]

    if (target) {
      target.value = filterValue(char, target.dataset.mode)
    }
  })

  const lastIndex = Math.min(
    startIndex + chars.length,
    inputs.length - 1
  )

  inputs[lastIndex]?.focus()
}

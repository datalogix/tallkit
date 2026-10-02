import { bind, emit, findFieldInput, setFieldValue, onLivewireCommit } from '../utils'

export function clearable() {
  return {
    destroy() {
      this._stopCommits?.()
    },

    init() {
      const button = this.$el

      if (this.clear) {
        bind(button, {
          ['@click']() {
            this.clear()
          }
        })
      }

      const input = findFieldInput(button)

      if (!input) {
        return
      }

      // No inline display: the stylesheet hides it on a disabled field.
      const sync = () => {
        button.style.display = input.value ? '' : 'none'
      }

      sync()

      bind(input, {
        ['@input']: sync,
      })

      this._stopCommits = onLivewireCommit(({ component, succeed }) => {
        if (!component?.el?.contains(input)) return

        succeed(() => this.$nextTick(sync))
      })

      bind(button, {
        ['@click']() {
          if (input.disabled || input.readOnly) return

          setFieldValue(input, '')
          emit(input, 'cleared', {}, { bubbles: true })
          input.focus()
        }
      })
    }
  }
}

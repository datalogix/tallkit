import { bind, emit, findFieldInput, setFieldValue, onLivewireCommit, onFormReset, listenWhileConnected } from '../utils'

export function clearable(action = null) {
  return {
    destroy() {
      this._stopCommits?.()

      if (this._modelEffect) window.Alpine?.release(this._modelEffect)
    },

    init() {
      const button = this.$el
      const input = findFieldInput(button)

      const sync = () => {
        if (input) button.style.display = input.value || input.validity?.badInput ? '' : 'none'
      }

      bind(button, {
        ['@click']() {
          if (input && (input.disabled || input.readOnly)) return

          if (action) window.Alpine.evaluate(button, action)

          setFieldValue(input, '')

          emit(input ?? button, 'cleared', {}, { bubbles: true })
          input?.focus()
          this.$nextTick(sync)
        }
      })

      if (!input) {
        return
      }

      bind(button, {
        ['@mousedown'](event) {
          event.preventDefault()
        }
      })

      sync()

      listenWhileConnected(button, input, 'input', sync)
      listenWhileConnected(button, input, 'change', sync)
      onFormReset(button, input.form, sync)

      this.$nextTick(() => {
        const model = input._x_model
        if (!model || !window.Alpine?.effect) return

        this._modelEffect = window.Alpine.effect(() => {
          model.get()
          this.$nextTick(sync)
        })
      })

      this._stopCommits = onLivewireCommit(({ component, succeed }) => {
        if (!component?.el?.contains(input)) return

        succeed(() => this.$nextTick(sync))
      })
    }
  }
}

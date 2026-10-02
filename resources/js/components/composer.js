import { queryData, bind, findInField, hasBlurModel, blurOnFocusLeave } from '../utils'
import { bindableField } from '../mixins/bindable-field'

export function composer({ submit = false } = {}) {
  const _bindableField = bindableField({ key: 'composer' })

  return {
    ..._bindableField,

    value: null,

    init() {
      _bindableField.init.call(this)

      // Its wire:model is on the root, which never blurs.
      if (hasBlurModel(this.$root)) blurOnFocusLeave(this.$root, this.$root)

      const modes = !submit ? [] : (Array.isArray(submit) ? submit : [submit])

      const control = queryData(this.$el, 'control')
      const labelFor = control && !control.id ? findInField(this.$el.parentElement, 'label')?.getAttribute('for') : null

      bind(control, {
        'x-model': 'value',
        ...(labelFor && { id: labelFor }),
        ...(modes.length && {
          ['@keydown'](e) {
            // Enter confirming an IME composition isn't a send.
            if (e.isComposing || e.keyCode === 229) return

            const shouldSubmit = modes.some((mode) => {
              switch (mode) {
                case 'enter':
                  return e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.metaKey

                case 'ctrl+enter':
                  return e.key === 'Enter' && (e.ctrlKey || e.metaKey)

                default:
                  return false
              }
            })

            if (!shouldSubmit) return
            e.preventDefault()

            if (!String(e.target.value ?? '').trim()) return

            this.$root?.closest('form')?.requestSubmit()
          },
        }),
      })
    },
  }
}

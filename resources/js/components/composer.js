import { dataKey, bind, findInField } from '../utils'
import { bindableField } from '../mixins/bindable-field'

export function composer({ submit = false, placeholder = false } = {}) {
  const _bindableField = bindableField({ key: 'composer' })

  return {
    ..._bindableField,

    value: null,

    init() {
      _bindableField.init.call(this)

      const modes = !submit ? [] : (Array.isArray(submit) ? submit : [submit])

      const labelFor = findInField(this.$el.parentElement, 'label')?.getAttribute('for') ?? null

      bind(this.$el.querySelector(dataKey('control')), {
        'x-model': 'value',
        ...(labelFor && { id: labelFor }),
        ...(placeholder && { placeholder }),
        ...(modes.length && {
          ['@keydown'](e) {
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
            this.$root?.closest('form')?.requestSubmit()
          },
        }),
      })
    },
  };
}

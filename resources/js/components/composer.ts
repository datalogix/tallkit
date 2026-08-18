import { dataKey, bind } from '../utils'

export function composer({ submit = false, placeholder = false } = {}) {
  return {
    value: null as string | null,

    init() {
      bind(this.$el, {
        'x-modelable': 'value',
      })

      const modes = !submit ? [] : (Array.isArray(submit) ? submit : [submit])

      const labelFor = this.$el.parentElement
        ?.closest(dataKey('field'))
        ?.querySelector(dataKey('label'))
        ?.getAttribute('for') ?? null

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
  }
}

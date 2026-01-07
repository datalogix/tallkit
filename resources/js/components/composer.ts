import { bind } from '../utils'

export function composer(submit?: string, placeholder?: string) {
  return {
    value: null,

    init () {
      bind(this.$el, {
        'x-modelable': 'value',
      })

      const labelFor = this.$el.parentElement?.querySelector('[data-tallkit-label]')?.getAttribute('for')

      bind(this.$el.querySelector('[data-tallkit-control]'), {
        'x-model': 'value',
        ...(labelFor ? { 'id': labelFor } : {}),
        ...(placeholder ? { 'placeholder': placeholder } : {}),
        ...(submit === 'enter' ? {
          ['@keydown.enter'](e) {
            if (e.shiftKey) return

            e.preventDefault()
            this.$root.closest('form')?.dispatchEvent(new Event('submit'))
          },
        } : {})
      })
    },
  }
}

import { bind, bindShortcut } from '../utils'

export function modalTrigger({ name = null, shortcut = null } = {}) {
  return {
    init() {
      bind(this.$el, {
        ['@click']() {
          if (this.$el.querySelector('button[disabled]')) {
            return
          }

          this.$dispatch('modal-show', { name })
        },
      })

      if (shortcut) {
        bindShortcut(this.$el, shortcut, () => this.$dispatch('modal-show', { name }))
      }
    }
  }
}

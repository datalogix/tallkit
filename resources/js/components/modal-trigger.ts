import { bind } from '../utils'

export function modalTrigger(name?: string, shortcut?: string) {
  return {
    init() {
      bind(this.$el, {
        ['@click']() {
          if (this.$el.querySelector('button[disabled]')) return
          this.$dispatch('modal-show', { name })
        },
      })

      if (shortcut) {
        bind(this.$el, {
          [`@keydown.${shortcut}.document`](event) {
            event.preventDefault()
            this.$dispatch('modal-show', { name })
          },
        })
      }
    }
  }
}

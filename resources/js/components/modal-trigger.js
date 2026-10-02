import { bind, bindShortcut, eventName } from '../utils'

export function modalTrigger({ name = null, shortcut = null } = {}) {
  return {
    init() {
      bind(this.$el, {
        ['@click']() {
          this.show()
        },
      })

      if (shortcut) {
        bindShortcut(this.$el, shortcut, () => this.show())
      }
    },

    show() {
      if (this.$root.querySelector('button[disabled]')) {
        return false
      }

      this.$dispatch(eventName('modal-show'), { name })
    }
  }
}

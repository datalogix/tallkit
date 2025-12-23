import { timeout as _timeout, type Milliseconds, bind } from '../utils'

export function alertComponent(timeout?: Milliseconds) {
  return {
    timeoutId: null,

    init() {
      bind(this.$el.querySelectorAll('[data-tallkit-alert-close]'), {
        ['@click']() {
          this.dismiss()
        }
      })

      if (timeout) {
        this.timeoutId = _timeout(() => this.dismiss(), timeout, 7000)
      }
    },

    dismiss() {
      clearTimeout(this.timeoutId)

      const root = this.$el.closest('[data-tallkit-alert]')

      if (!root) {
        return
      }

      root.classList.remove('opacity-100')
      root.classList.add('opacity-0')

      root.addEventListener(
        'transitionend',
        () => root?.remove(),
        { once: true }
      )
    }
  }
}

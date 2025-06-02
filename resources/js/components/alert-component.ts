import { timeout as _timeout, type Milliseconds } from "../utils/timeout"

export function alertComponent(timeout?: Milliseconds) {
  return {
    init() {
      if (timeout) {
        return _timeout(() => this.dismiss(), timeout, 7000)
      }
    },

    dismiss() {
      const root = this.$el.closest('[data-tallkit-alert]')

      root.classList.remove('opacity-100')
      root.classList.add('opacity-0')

      root.addEventListener(
        'transitionend',
        () => root.remove(),
        { once: true }
      )
    }
  }
}

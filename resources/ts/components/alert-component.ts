import { timeout as _timeout, type Milliseconds } from "../utils/timeout"

export function alertComponent(timeout?: Milliseconds) {
  return {
    init() {
      if (timeout) {
        return _timeout(() => this.dismiss(), timeout, 7000)
      }
    },

    dismiss() {
      this.$root.classList.remove('opacity-100')
      this.$root.classList.add('opacity-0')

      this.$root.addEventListener(
        'transitionend',
        () => this.$root.remove(),
        { once: true }
      )
    }
  }
}

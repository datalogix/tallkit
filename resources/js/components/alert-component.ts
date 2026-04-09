import { timeout as _timeout, type Milliseconds, bind, collapseAndRemove } from '../utils'

export function alertComponent(timeout?: Milliseconds, animation?: boolean) {
  return {
    timeoutId: null,
    cancelDismiss: null,
    isDismissing: false,

    init() {
      bind(this.$el.querySelectorAll('[data-tallkit-alert-close]'), {
        ['@click']: () => this.dismiss()
      })

      if (timeout) {
        this.timeoutId = _timeout(() => this.dismiss(), timeout, 7000)
      }

      this.$el.addEventListener('alpine:destroy', () => this.cleanup())
    },

    cleanup() {
      clearTimeout(this.timeoutId)

      if (this.cancelDismiss) {
        this.cancelDismiss()
      }

      this.timeoutId = null
      this.cancelDismiss = null
      this.isDismissing = false
    },

    dismiss() {
      if (this.isDismissing) {
        return
      }

      this.isDismissing = true
      clearTimeout(this.timeoutId)
      this.timeoutId = null

      const root = this.$el.closest('[data-tallkit-alert]') as HTMLElement | null

      if (!root) {
        this.isDismissing = false
        return
      }

      root.classList.remove('opacity-100')
      root.classList.add('opacity-0')

      this.cancelDismiss = collapseAndRemove(root, {
        animated: animation,
        onDone: () => {
          this.cancelDismiss = null
          this.isDismissing = false
        }
      })
    }
  }
}

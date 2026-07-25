import { bind, fadeOut, collapse } from '../utils'

export function dismissible(animation?: 'fade' | 'collapse') {
  return {
    cancelDismiss: null as (() => void) | null,
    isDismissing: false,

    init() {
      bind(this.$root.querySelectorAll('[data-tallkit-dismissible]'), {
        ['@click.stop']: (e) => {
          e.currentTarget.dispatchEvent(new CustomEvent('close'))
          this.dismiss('manual')
        }
      })

      bind(this.$root, {
        ['@dismiss']: (e: Event) => {
          const detail = (e as CustomEvent).detail || {}
          this.dismiss(detail.reason || 'programmatic')
        },
      })
    },

    beforeDismiss() {
      // Placeholder for any preparation logic before dismissal
    },

    dismiss(reason = 'programmatic') {
      if (this.isDismissing) return

      const event = new CustomEvent('before-dismiss', {
        detail: { reason },
        cancelable: true
      })

      this.$root.dispatchEvent(event)

      if (event.defaultPrevented) {
        return
      }

      this.isDismissing = true
      this.beforeDismiss()

      this.cancelDismiss?.()
      this.cancelDismiss = null

      const onDone = () => {
        this.isDismissing = false
        this.cancelDismiss = null
        this.$dispatch('dismissed', { reason })
      }

      if (animation === 'fade') {
        this.cancelDismiss = fadeOut(this.$root, {
          remove: true,
          onDone
        })
      } else if (animation === 'collapse') {
        this.cancelDismiss = collapse(this.$root, {
          remove: true,
          onDone
        })
      } else {
        this.$root.remove()
        onDone()
      }

      setTimeout(() => {
        this.isDismissing = false
      }, 2000)
    },

    destroy() {
      this.cancelDismiss?.()
      this.cancelDismiss = null
      this.isDismissing = false
    },
  }
}

import { dataKey, bind, fadeOut, collapse, getTransitionTimeout } from '../utils'

export function dismissible(animation?: 'fade' | 'collapse') {
  return {
    cancelDismiss: null as (() => void) | null,
    isDismissing: false,
    _dismissTimeout: null as ReturnType<typeof setTimeout> | null,

    init() {
      bind(this.$root.querySelectorAll(dataKey('dismissible')), {
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

        if (this.$root.isConnected) {
          this.$root.remove()
        }
      }

      if (animation === 'fade') {
        this.cancelDismiss = fadeOut(this.$root, { onDone })
      } else if (animation === 'collapse') {
        this.cancelDismiss = collapse(this.$root, { onDone })
      } else {
        onDone()
      }

      if (this._dismissTimeout) {
        clearTimeout(this._dismissTimeout)
      }

      this._dismissTimeout = setTimeout(() => {
        this.isDismissing = false
        this._dismissTimeout = null
      }, Math.max(getTransitionTimeout(this.$root) * 1.5, 500))
    },

    destroy() {
      this.cancelDismiss?.()
      this.cancelDismiss = null
      this.isDismissing = false

      if (this._dismissTimeout) {
        clearTimeout(this._dismissTimeout)
        this._dismissTimeout = null
      }
    },
  }
}

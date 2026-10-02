import { dataSelector, bind, fadeOut, collapse, getTransitionTimeout, keepDismissed, hasLivewire, focusTargetOutside, emit, eventName } from '../utils'

function closestDismissible(el) {
  for (let node = el; node; node = node.parentElement) {
    if (node.__tallkitDismissible) return node
  }

  return null
}

export function dismissible(animation) {
  return {
    cancelDismiss: null,
    isDismissing: false,
    _dismissTimeout: null,

    init() {
      // A property, not an attribute: Livewire's morph would remove an attribute.
      this.$root.__tallkitDismissible = true

      bind(this.$root, {
        ['@click']: (event) => {
          const trigger = event.target.closest?.(dataSelector('dismissible'))

          if (!trigger || !this.$root.contains(trigger)) return
          if (closestDismissible(trigger) !== this.$root) return

          event.stopPropagation()
          this.dismiss('manual')
        },

        [`@${eventName('dismiss')}`]: (e) => {
          const detail = e.detail || {}
          this.dismiss(detail.reason || 'programmatic')
        },
      })
    },

    beforeDismiss() {
    },

    dismiss(reason = 'programmatic') {
      if (this.isDismissing) return

      const event = emit(this.$root, 'before-dismiss', { reason }, { cancelable: true })

      if (event?.defaultPrevented) {
        return
      }

      this.isDismissing = true
      this.beforeDismiss()

      const focusTarget = this.$root.contains(document.activeElement) ? focusTargetOutside(this.$root) : null

      this.cancelDismiss?.()
      this.cancelDismiss = null

      const onDone = () => {
        this.isDismissing = false
        this.cancelDismiss = null
        emit(this.$root, 'dismissed', { reason })

        focusTarget?.isConnected && focusTarget.focus({ preventScroll: true })

        if (!this.$root.isConnected) return

        // Hidden, not removed, inside Livewire: the next update would bring it back.
        if (hasLivewire() && this.$root.closest('[wire\\:id]')) {
          keepDismissed(this.$root)
        } else {
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

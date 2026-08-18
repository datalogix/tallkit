import { bind, bindShortcut } from '../utils'

export function modal({ name = null, dismissible = null, persist = null, shortcut = null } = {}) {
  return {
    init() {
      const dialog = this.$el

      bind(dialog.querySelectorAll('[data-tallkit-modal-close],[data-tallkit-modal-auto-close]'), {
        ['@click']() {
          dialog.close()
        },
      })

      bind(dialog, {
        ['@modal-show.document'](event) {
          if (event.detail.name === name && !event.detail.scope) {
            dialog.showModal()
            return
          }

          if (event.detail.name === name && event.detail.scope === this.$wire?.id) {
            dialog.showModal()
            return
          }
        },

        ['@modal-close.document'](event) {
          if (!event.detail.name || (event.detail.name === name && !event.detail.scope)) {
            dialog.close()
            return
          }

          if (event.detail.name === name && event.detail.scope === this.$wire?.id) {
            dialog.close()
            return
          }
        },
      })

      const handleCloseAttempt = (event) => {
        event.preventDefault()

        if (persist) {
          const persistAnimation = typeof persist === 'string' ? persist : 'tilt-shaking'
          dialog.classList.remove(persistAnimation)
          dialog.focus()

          this.$nextTick(() => dialog.classList.add(persistAnimation))

          return
        }

        if (dismissible !== false && (event.target === dialog || event.target.getAttribute('tabindex') === '0')) {
          dialog.close()
        }
      }

      bind(dialog, {
        ['@toggle'](event) {
          if (event.newState === 'open') {
            dialog.querySelector('[tabindex="0"]')?.focus()
            this.$dispatch('opened', event)
          }

          if (event.newState === 'closed') {
            this.$dispatch('closed', event)
          }
        },

        ['@click'](event) {
          handleCloseAttempt(event)
        },

        ['@keydown.escape.prevent'](event) {
          handleCloseAttempt(event)
        },
      })

      if (shortcut) {
        bindShortcut(dialog, shortcut, () => this.$dispatch('modal-show', { name }))
      }
    },

    show() {
      this.$dispatch('modal-show', { name })
    },

    close() {
      this.$dispatch('modal-close', { name })
    }
  }
}

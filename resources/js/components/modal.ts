import { bind } from '../utils'

export function modal(name?: string, dismissible?: boolean, persist?: string | boolean) {
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
          if (event.detail.name === name && event.detail.scope === this.$wire?.id) dialog.showModal()
          if (event.detail.name === name && !event.detail.scope) dialog.showModal()
        },

        ['@modal-close.document'](event) {
          if (event.detail.name === name && event.detail.scope === this.$wire?.id) dialog.close()
          if (!event.detail.name || (event.detail.name === name && !event.detail.scope)) dialog.close()
        },
      })

      const handleCloseAttempt = (event) => {
        if (persist) {
          const persistAnimation = typeof persist === 'string' ? persist : 'tilt-shaking'
          dialog.classList.remove(persistAnimation)
          this.$nextTick(() => dialog.classList.add(persistAnimation))

          return
        }

        if (dismissible !== false && event.target === dialog || event.target.getAttribute('tabindex') === '0') {
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

        ['@keyup.escape.window'](event) {
          handleCloseAttempt(event)
        },
      })
    }
  }
}

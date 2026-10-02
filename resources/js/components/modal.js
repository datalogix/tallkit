import { dataSelector, bind, bindShortcut, isEscapeHandled, emit, eventName } from '../utils'

export function modal(
  {
    name = null,
    dismissible = null,
    persist = null,
    shortcut = null,
    open = false
  } = {}
) {
  return {
    init() {
      const dialog = this.$el

      bind(dialog, {
        [`@${eventName('modal-show')}.document`](event) {
          if (event.detail.name === name && !event.detail.scope) {
            dialog.showModal()
            return
          }

          if (event.detail.name === name && event.detail.scope === this.$wire?.id) {
            dialog.showModal()
            return
          }
        },

        [`@${eventName('modal-close')}.document`](event) {
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

      // A nested modal's clicks and keys bubble up here too.
      const fromInnerModal = (event) => event.target instanceof Element && event.target.closest('dialog') !== dialog

      // Where the press began: a selection ending over the backdrop is a click on the dialog.
      let pressedOn = null

      const handleCloseAttempt = (event, checkTarget = true) => {
        if (checkTarget) {
          const target = event.target
          const started = pressedOn

          pressedOn = null

          if (target !== dialog || started !== dialog) {
            return
          }
        }

        event.preventDefault()

        if (persist) {
          const persistAnimation = typeof persist === 'string' ? persist : 'tilt-shaking'
          // Taken off and put back next tick, so the animation replays.
          dialog.classList.remove(persistAnimation)
          dialog.focus()

          this.$nextTick(() => dialog.classList.add(persistAnimation))

          return
        }

        if (dismissible === false) {
          return
        }

        dialog.close()
      }

      bind(dialog, {
        ['@toggle'](event) {
          if (event.newState === 'open') {
            const autofocus = Array.from(dialog.querySelectorAll('[autofocus]')).find((el) => el.closest('dialog') === dialog)
            const title = dialog.getAttribute('aria-labelledby') ? document.getElementById(dialog.getAttribute('aria-labelledby')) : null
            const start = autofocus ?? (title && dialog.contains(title) ? title : dialog)

            if (!autofocus && !start.hasAttribute('tabindex')) {
              start.setAttribute('tabindex', '-1')
              start.style.outline = 'none'
            }

            start.focus()
            emit(dialog, 'opened', { name })
          }

          if (event.newState === 'closed') {
            emit(dialog, 'closed', { name })
          }
        },

        ['@pointerdown'](event) {
          pressedOn = event.target
        },

        ['@click'](event) {
          if (fromInnerModal(event)) return

          if ((event.target).closest(`${dataSelector('modal-close')},${dataSelector('modal-auto-close')}`)) {
            dialog.close()
            return
          }

          handleCloseAttempt(event)
        },

        ['@keydown.escape.prevent'](event) {
          // .prevent keeps the browser from closing the dialog.
          if (fromInnerModal(event) || isEscapeHandled(event)) return

          handleCloseAttempt(event, false)
        },

        // Android's back button cancels without an Escape: the same rules apply.
        ['@cancel'](event) {
          if (event.target !== dialog) return

          handleCloseAttempt(event, false)
        },
      })

      if (shortcut) {
        bindShortcut(dialog, shortcut, () => this.$dispatch(eventName('modal-show'), { name }))
      }

      if (open) this.$nextTick(() => dialog.isConnected && !dialog.open && dialog.showModal())
    },

    show() {
      this.$dispatch(eventName('modal-show'), { name })
    },

    close() {
      this.$dispatch(eventName('modal-close'), { name })
    }
  }
}

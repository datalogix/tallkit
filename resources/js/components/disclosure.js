import { bind, dataSelector, generateId, keepAttributesOnMorph } from '../utils'
import { toggleable } from '../mixins/toggleable'

export const TRIGGER_SELECTOR = ':scope > button, :scope > [role="heading"] > button'

export function disclosure() {
  const _toggleable = toggleable()

  return {
    ..._toggleable,

    observer: null,

    init() {
      _toggleable.init.call(this, this.$root.hasAttribute('data-open'))

      const panel = this.$root.querySelector(':scope > button + *, :scope > [role="heading"] + *')

      if (panel && !panel.id) {
        panel.id = generateId('disclosure')
      }

      const buttons = Array.from(this.$root.querySelectorAll(TRIGGER_SELECTOR))

      buttons.forEach((button) => {
        if (!button.id) button.id = generateId('disclosure', null, 'trigger')
      })

      if (panel?.getAttribute('role') === 'region' && buttons[0] && !panel.hasAttribute('aria-labelledby')) {
        panel.setAttribute('aria-labelledby', buttons[0].id)
      }

      this.observer = new MutationObserver(() => {
        const opened = this.$root.hasAttribute('data-open')

        if (this.$root.closest(dataSelector('disclosure-settling'))) {
          this.opened = opened
        } else {
          this.setOpened(opened)
        }
      })

      this.observer.observe(this.$root, { attributeFilter: ['data-open'] })

      const stops = [
        keepAttributesOnMorph((el) => buttons.includes(el), ['id', 'aria-controls', 'aria-expanded']),
        keepAttributesOnMorph((el) => el === panel, ['id', 'aria-labelledby']),
        window.Livewire?.hook('morph.updating', ({ el, toEl }) => {
          if (el !== this.$root || !toEl?.querySelectorAll) return

          const incoming = [...toEl.querySelectorAll(TRIGGER_SELECTOR), toEl.querySelector(':scope > button + *, :scope > [role="heading"] + *')]

          ;[...buttons, panel].forEach((node, index) => {
            if (node && incoming[index]) incoming[index].id ||= node.id
          })
        }),
      ]

      this._stopMorphHook = () => stops.forEach((stop) => stop?.())

      bind(buttons, {
        [':aria-controls']() {
          return panel?.id ?? null
        },

        [':aria-expanded']() {
          return String(this.opened)
        },

        ['@click']() {
          this.toggle()
        }
       })
    },

    open() {
      this.$root.setAttribute('data-open', '')
      _toggleable.open.call(this)
    },

    close() {
      this.$root.removeAttribute('data-open')
      _toggleable.close.call(this)
    },

    destroy() {
      this.observer?.disconnect()
      this._stopMorphHook?.()
    },
  }
}

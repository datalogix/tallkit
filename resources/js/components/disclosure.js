import { bind, generateId } from '../utils'
import { toggleable } from '../mixins/toggleable'

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

      this.observer = new MutationObserver(() => this.setOpened(this.$root.hasAttribute('data-open')))
      this.observer.observe(this.$root, { attributeFilter: ['data-open'] })

      bind(this.$root.querySelectorAll(':scope > button, :scope > [role="heading"] > button'), {
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
    },
  }
}

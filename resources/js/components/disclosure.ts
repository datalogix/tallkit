import { bind } from '../utils'
import { toggleable } from './toggleable'

export function disclosure() {
  const _toggleable = toggleable()

  return {
    ..._toggleable,

    init() {
      _toggleable.init.call(this, this.$root.hasAttribute('data-open'))

      new MutationObserver(() => { this.opened = this.$root.hasAttribute('data-open') })
        .observe(this.$root, { attributeFilter: ['data-open'] })

      bind(this.$root.querySelectorAll(':scope > button'), {
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
  }
}

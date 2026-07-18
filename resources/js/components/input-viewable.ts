import { bind } from '../utils'

export function inputViewable() {
  return {
    viewed: false,

    init() {
      const input = this.$el
        ?.closest('[data-tallkit-field-control]')
        ?.querySelector('[data-tallkit-input]')

      if (!input) {
        return
      }

      input.setAttribute('type', this.viewed ? 'text' : 'password')

      bind(this.$el, {
        ['@click']() {
          this.viewed = !this.viewed
          input.setAttribute('type', this.viewed ? 'text' : 'password')
          input.dispatchEvent(new Event('viewed', { bubbles: false }))
        }
      })

      const inputObserver = new MutationObserver(() => {
        this.viewed = input?.getAttribute('type') !== 'password'
      })

      inputObserver.observe(input, {
        attributes: true,
        attributeFilter: ['type']
      })
    }
  }
}

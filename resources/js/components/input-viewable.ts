import { bind } from '../utils'

export function inputViewable() {
  return {
    viewed: false,

    get input () {
      return this.$el.closest('[data-tallkit-field-control]')?.querySelector('input')
    },

    init() {
      if (!this.input) {
        return
      }

      this.input.setAttribute('type', this.viewed ? 'text' : 'password')

      bind(this.$el, {
        ['@click']() {
          this.viewed = !this.viewed
          this.input.setAttribute('type', this.viewed ? 'text' : 'password')
          this.input.dispatchEvent(new Event('viewed', { bubbles: false }))
        }
      })

      const inputObserver = new MutationObserver(() => {
        this.viewed = this.input?.getAttribute('type') !== 'password'
      })

      inputObserver.observe(this.input, {
        attributes: true,
        attributeFilter: ['type']
      })
    }
  }
}

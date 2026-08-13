import { bind, findFieldInput } from '../utils'

export function inputViewable() {
  return {
    viewed: false,
    inputObserver: null,
    originalType: 'password',

    init() {
      const input = findFieldInput(this.$el)

      if (!input) {
        return
      }

      if (input.type) {
        this.originalType = input.type
      }

      input.setAttribute('type', this.viewed ? 'text' : this.originalType)

      bind(this.$el, {
        [':aria-pressed']() {
          return this.viewed
        },

        ['@click']() {
          this.viewed = !this.viewed
          input.setAttribute('type', this.viewed ? 'text' : this.originalType)
          input.dispatchEvent(new Event('viewed', { bubbles: true }))
        }
      })

      this.inputObserver = new MutationObserver(() => {
        this.viewed = input?.getAttribute('type') !== 'password'
      })

      this.inputObserver.observe(input, {
        attributes: true,
        attributeFilter: ['type']
      })
    },

    destroy() {
      this.inputObserver?.disconnect()
    }
  }
}

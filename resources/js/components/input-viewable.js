import { bind, emit, findFieldInput, keepAttributesOnMorph } from '../utils'

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
        ['@click']() {
          this.viewed = !this.viewed
          input.setAttribute('type', this.viewed ? 'text' : this.originalType)
          emit(input, 'viewed', {}, { bubbles: true })
        }
      })

      this.inputObserver = new MutationObserver(() => {
        this.viewed = input?.getAttribute('type') !== 'password'
      })

      this.inputObserver.observe(input, {
        attributes: true,
        attributeFilter: ['type']
      })

      this._stopMorphHook = keepAttributesOnMorph((el) => el === input, ['type'])
    },

    destroy() {
      this.inputObserver?.disconnect()
      this._stopMorphHook?.()
    }
  }
}

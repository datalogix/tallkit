import { bind } from "../utils"

export function inputViewable() {
  return {
    viewed: false,

    get input () {
      return this.$el.closest('[data-tallkit-input]').querySelector('input')
    },

    init() {
      if (!this.input) {
        return
      }

      bind(this.$el, {
        ['@click']() {
          this.viewed = !this.viewed
          this.input.setAttribute('type', this.viewed ? 'text' : 'password')
        }
      })

      let observer = new MutationObserver(() => {
        this.viewed = this.input.getAttribute('type') !== 'password'
      })

      observer.observe(this.input, {
        attributes: true,
        attributeFilter: ['type']
      })
    }
  }
}

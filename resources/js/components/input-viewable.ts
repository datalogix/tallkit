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
        let type = this.viewed ? 'text' : 'password';
        if (this.input.getAttribute('type') === type) return
        this.input.setAttribute('type', type)
      })

      observer.observe(this.input, {
        attributes: true,
        attributeFilter: ['type']
      })
    }
  }
}

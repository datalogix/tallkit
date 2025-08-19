import { bind } from '../utils'

export function menuCheckbox() {
  return {
    get checked() {
      return this.$el.hasAttribute('data-checked')
    },

    set checked(value) {
      if (value) {
        this.$el.setAttribute('data-checked', '')
      } else {
        this.$el.removeAttribute('data-checked')
      }
    },

    init() {
      this.$el.setAttribute('aria-checked', this.checked ? 'true' : 'false')

      new MutationObserver(() => {
        this.$el.setAttribute('aria-checked', this.checked ? 'true' : 'false')
      }).observe(this.$el, { attributeFilter: ['data-checked'] })

      bind(this.$el, {
        ['@click']() {
          if (this.$el.disabled) {
            return
          }

          this.checked = !this.checked
        },
      })
    }
  }
}

import { bind } from '../utils'

export function menu() {
  return {
    init() {
      bind(this.$el.querySelectorAll('[data-tallkit-menu-item]'), {
        ['@mouseenter']() {
          if (this.$el.disabled) {
            return
          }

          this.$el.setAttribute('data-active', '')
        },

        ['@mouseleave']() {
          if (this.$el.disabled) {
            return
          }

          this.$el.removeAttribute('data-active')
        },
      })
    }
  }
}

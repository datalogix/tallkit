import { dataKey, bind } from '../utils'

export function menu() {
  return {
    init() {
      const items = (Array.from(this.$el.querySelectorAll(dataKey('menu-item'))))
        .filter((item) => item.closest(dataKey('menu')) === this.$el)

      bind(items, {
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

        ['@focus']() {
          if (this.$el.disabled) {
            return
          }

          this.$el.setAttribute('data-active', '')
        },

        ['@blur']() {
          this.$el.removeAttribute('data-active')
        },
      })

      bind(this.$el, {
        ['@keydown.arrow-down.prevent']() {
          this.focusItem(items, 1)
        },

        ['@keydown.arrow-up.prevent']() {
          this.focusItem(items, -1)
        },

        ['@keydown.home.prevent']() {
          this.focusItem(items, 'first')
        },

        ['@keydown.end.prevent']() {
          this.focusItem(items, 'last')
        },
      })
    },

    focusItem(items, direction) {
      const enabled = items.filter((item) => !item.disabled)
      if (!enabled.length) return

      const currentIndex = enabled.indexOf(document.activeElement)
      let index

      if (direction === 'first') {
        index = 0
      } else if (direction === 'last') {
        index = enabled.length - 1
      } else if (currentIndex === -1) {
        index = direction === 1 ? 0 : enabled.length - 1
      } else {
        index = (currentIndex + direction + enabled.length) % enabled.length
      }

      enabled[index].focus()
    }
  };
}

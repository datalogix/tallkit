import { bind } from '../utils'

export function badge() {
  return {
    init() {
      bind(this.$el.querySelector('[data-tallkit-badge-close]'), {
        ['@click']: () => this.close()
      })
    },

    close() {
      const root = this.$el.closest('[data-tallkit-badge]')

      if (!root) {
        return
      }

      root.classList.remove('opacity-100')
      root.classList.add('opacity-0')

      root.addEventListener(
        'transitionend',
        () => {
          root?.remove()
          this.$dispatch('close')
        },
        { once: true }
      )
    }
  }
}

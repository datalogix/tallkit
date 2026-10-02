import { bind, isRtl } from '../utils'

const MODES = ['system', 'light', 'dark']

export function appearanceSelector() {
  return {
    init() {
      bind(this.$root, {
        ['@keydown.right.prevent']: () => this.move(isRtl(this.$root) ? -1 : 1),
        ['@keydown.down.prevent']: () => this.move(1),
        ['@keydown.left.prevent']: () => this.move(isRtl(this.$root) ? 1 : -1),
        ['@keydown.up.prevent']: () => this.move(-1),
      })

      bind(this.$root.querySelectorAll('[data-mode]'), (button) => {
        const mode = button.dataset.mode

        return {
          ['@click']: () => this.$tallkit.appearance.apply(mode),
          [':aria-checked']: () => this.$tallkit.appearance.mode === mode,
          [':tabindex']: () => (this.$tallkit.appearance.mode === mode ? 0 : -1),
        }
      })
    },

    move(step) {
      const next = MODES[(MODES.indexOf(this.$tallkit.appearance.mode) + step + MODES.length) % MODES.length]
      this.$tallkit.appearance.apply(next)
      this.$nextTick(() => this.$root.querySelector(`[data-mode='${next}']`)?.focus())
    },
  }
}

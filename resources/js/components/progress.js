import { clamp, toNumber } from '../utils'

export function progress(percentage = null) {
  return {
    value: 0,

    init() {
      this.updateValue(percentage ?? 0)
    },

    updateValue(n) {
      const num = toNumber(n)

      if (num === null) return

      this.value = clamp(num, 0, 100)
    }
  }
}

export function progressBar() {
  return {
    paint(value) {
      const percent = clamp(toNumber(value, 0), 0, 100)
      const bar = this.$root

      // Going back (a restart) jumps instead of sliding backwards.
      bar.style.transition = percent < toNumber(bar.dataset.value, 0) ? 'none' : ''
      bar.style.width = `${percent}%`
      bar.dataset.value = percent
    },
  }
}

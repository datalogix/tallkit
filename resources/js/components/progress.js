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

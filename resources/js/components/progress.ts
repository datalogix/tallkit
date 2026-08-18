export function progress(percentage = null) {
  return {
    value: 0,

    init () {
      this.updateValue(percentage ?? 0)
    },

    updateValue (n) {
      const num = Number(n)

      if (Number.isNaN(num)) {
        return
      }

      this.value = Math.max(0, Math.min(100, num))
    }
  }
}

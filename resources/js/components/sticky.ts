export function sticky() {
  return {
    init() {
      const e = this.$el.offsetTop
      this.$el.style.position = 'sticky'
      this.$el.style.top = `${e}px`
      this.$el.style.maxHeight = `calc(100dvh - ${e}px)`
    }
  }
}

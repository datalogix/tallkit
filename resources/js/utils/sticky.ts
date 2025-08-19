export function sticky() {
  return {
    init() {
      this.$el.style.position = 'sticky'
      this.$el.style.top = `${this.$el.offsetTop}px`
      this.$el.style.maxHeight = `calc(100dvh - ${this.$el.offsetTop}px)`
    }
  }
}

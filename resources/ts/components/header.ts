export function header() {
  return {
    init() {
      this.$el.style.position = 'sticky'
      this.$el.style.top = `${this.$el.offsetTop}px`,
      this.$el.style.maxHeight = `calc(100vh - ${this.$el.offsetTop}px)`
    }
  }
}

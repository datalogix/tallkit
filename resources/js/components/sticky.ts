export function sticky() {
  return {
    init() {
      this.updateOffset()
      this._onResize = () => this.updateOffset()
      window.addEventListener('resize', this._onResize)
    },

    updateOffset() {
      const top = this.$el.offsetTop
      this.$el.style.position = 'sticky'
      this.$el.style.top = `${top}px`
      this.$el.style.maxHeight = `calc(100dvh - ${top}px)`
    },

    destroy() {
      window.removeEventListener('resize', this._onResize)
    }
  }
}

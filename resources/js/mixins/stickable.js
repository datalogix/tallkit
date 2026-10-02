export function stickable() {
  return {
    _onResize: null,
    _resizeObserver: null,

    init() {
      this.updateOffset()

      this._onResize = () => this.updateOffset()
      window.addEventListener('resize', this._onResize)

      this._resizeObserver = new ResizeObserver(() => this.updateOffset())
      this._resizeObserver.observe(document.body)
    },

    updateOffset() {
      // Measured in the flow: once stuck, offsetTop is where it is stuck.
      this.$el.style.position = 'static'
      const top = this.$el.offsetTop
      this.$el.style.position = 'sticky'
      this.$el.style.top = `${top}px`
      this.$el.style.maxHeight = `calc(100dvh - ${top}px)`
    },

    destroy() {
      window.removeEventListener('resize', this._onResize)
      this._resizeObserver?.disconnect()
    }
  }
}

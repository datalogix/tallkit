export function fieldControl() {
  return {
    control: null,
    mutationObserver: null,

    init () {
      this.control = this.$el.querySelector('[data-tallkit-control]')

      if (!this.control) return

      this.calculate()

      this.mutationObserver = new MutationObserver(() => {
        this.calculate()
      })

      this.mutationObserver.observe(this.$el, {
        childList: true,
        subtree: true
      })
    },

    calculate () {
      const styles = getComputedStyle(this.control)
      const paddingTop = parseFloat(styles.paddingTop) || 0
      const paddingStart = parseFloat(styles.paddingInlineStart) || 0
      const paddingEnd = parseFloat(styles.paddingInlineEnd) || 0

      this.control.style.paddingInlineStart = ''
      this.control.style.paddingInlineEnd = ''

      const prependEl = this.$el.querySelector('[data-tallkit-field-control-prepend]')
      const appendEl = this.$el.querySelector('[data-tallkit-field-control-append]')

      if (prependEl) {
        prependEl.style.paddingTop = `${paddingTop}px`
        prependEl.style.paddingInlineStart = `${paddingStart}px`
        //prependEl.style.paddingInlineEnd = `${paddingStart}px`
        this.control.style.paddingInlineStart =`${parseFloat(getComputedStyle(prependEl).width)}px`
      }

      if (appendEl) {
        appendEl.style.paddingTop = `${paddingTop}px`
        //appendEl.style.paddingInlineStart = `${paddingEnd}px`
        appendEl.style.paddingInlineEnd = `${paddingEnd}px`
        this.control.style.paddingInlineEnd = `${parseFloat(getComputedStyle(appendEl).width)}px`
      }
    }
  }
}

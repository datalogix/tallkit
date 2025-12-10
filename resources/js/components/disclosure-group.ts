export function disclosureGroup(exclusive = false) {
  return {
    init () {
      if (exclusive) {
        this.initExclusive()
      }
    },

    initExclusive () {
      const items = this.$root.querySelectorAll('[data-tallkit-disclosure-item]')

      const observe = () => {
        items.forEach((item) => {
          observer.observe(item, { attributeFilter: ['data-open'] })
        })
      }

      const observer = new MutationObserver((records) => {
        const current = records[0]?.target

        items.forEach((item) => {
          if (item === current) return

          if (item._x_dataStack && item?._x_dataStack[0] && typeof item?._x_dataStack[0].close === 'function') {
            item?._x_dataStack[0].close()
          } else {
            item.removeAttribute('data-open')
          }
        })

        observer.disconnect()

        this.$nextTick(observe)
      })

      observe()
    }
  }
}

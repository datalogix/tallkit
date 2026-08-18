import { dataKey } from '../utils'

export function disclosureGroup({ exclusive = false } = {}) {
  return {
    observer: null,

    init () {
      const items = this.$root.querySelectorAll(dataKey('disclosure-item'))

      const observe = () => {
        items.forEach((item) => {
          this.observer.observe(item, { attributeFilter: ['data-open'] })
        })
      }

      this.observer = new MutationObserver((records) => {
        if (exclusive) {
          const opened = new Set(
            records
              .filter(record => record.target.hasAttribute('data-open'))
              .map(record => record.target)
          )

          items.forEach((item) => {
            if (opened.has(item)) return

            item.removeAttribute('data-open')
          })
        }

        this.observer.disconnect()
        this.$dispatch('changed', { items })
        this.$nextTick(observe)
      })

      observe()
    },

    destroy () {
      this.observer?.disconnect()
    }
  }
}

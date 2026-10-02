import { dataSelector, queryAllData, emit } from '../utils'

export function disclosureGroup({ exclusive = false } = {}) {
  return {
    observer: null,

    init() {
      const own = (el) => el.matches?.(dataSelector('disclosure-item')) && el.closest('[x-data^="disclosureGroup"]') === this.$root
      const getItems = () => queryAllData(this.$root, 'disclosure-item').filter(own)

      const observe = () => this.observer.observe(this.$root, { subtree: true, attributeFilter: ['data-open'] })

      this.observer = new MutationObserver((records) => {
        const changed = records.map((record) => record.target).filter(own)

        if (!changed.length) return

        const items = getItems()

        if (exclusive) {
          const opened = new Set(changed.filter((item) => item.hasAttribute('data-open')))

          if (opened.size) {
            items.forEach((item) => {
              if (!opened.has(item)) item.removeAttribute('data-open')
            })
          }
        }

        // Disconnecting drops the records of the ones closed just now.
        this.observer.disconnect()
        emit(this.$root, 'changed', { items })
        this.$nextTick(observe)
      })

      observe()
    },

    destroy() {
      this.observer?.disconnect()
    }
  }
}

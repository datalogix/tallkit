import { loadRemoteAssets } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { loadable } from './loadable'

export function frappeCharts() {
  const _loadable = loadable()

  return {
    ..._loadable,
    ...dataOptions(),

    chart: null,

    init() {
      this.load(() => loadRemoteAssets(() => !!window.frappe?.Chart, 'https://cdn.jsdelivr.net/npm/frappe-charts@1'))
    },

    render(options = {}) {
      try {
        this.chart?.destroy?.()
        this.chart = new window.frappe.Chart(this.$refs.target, { ...options, ...this.getDataOptions() })
        this.$dispatch('rendered', { chart: this.chart })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.chart?.destroy?.()
      this.chart = null
    }
  }
}

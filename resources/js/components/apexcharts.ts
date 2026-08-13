import { loadRemoteAssets } from '../utils'
import { dataOptions } from './data-options'
import { loadable } from './loadable'

export function apexcharts() {
  const _loadable = loadable()

  return {
    ..._loadable,
    ...dataOptions(),

    chart: null,

    init() {
      this.load(() => loadRemoteAssets(() => !!window.ApexCharts, 'https://cdn.jsdelivr.net/npm/apexcharts@5'))
    },

    render(options = {}) {
      const merged = { ...options, ...this.getDataOptions() }

      if (this.chart) {
        this.chart.updateOptions(merged)
      } else {
        this.chart = new window.ApexCharts(this.$el, merged)
        this.chart.render()
      }

      this.$dispatch('rendered', { chart: this.chart })
    },

    destroy() {
      _loadable.destroy.call(this)
      this.chart?.destroy()
      this.chart = null
    }
  }
}

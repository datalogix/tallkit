import { loadRemoteAssets } from '../utils'
import { dataOptions } from '../mixins/data-options'
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
      try {
        const merged = { ...options, ...this.getDataOptions() }

        if (this.chart) {
          this.chart.updateOptions(merged)
        } else {
          this.chart = new window.ApexCharts(this.$refs.target, merged)
          this.chart.render()
        }

        this.$dispatch('rendered', { chart: this.chart })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.chart?.destroy()
      this.chart = null
    }
  }
}

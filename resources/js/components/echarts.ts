import { loadRemoteAssets } from '../utils'
import { dataOptions } from './data-options'
import { loadable } from './loadable'

export function echarts() {
  const _loadable = loadable()

  return {
    ..._loadable,
    ...dataOptions(),

    chart: null,

    init() {
      this.load(() => loadRemoteAssets(() => !!window.echarts, 'https://cdn.jsdelivr.net/npm/echarts@6'))
    },

    render(options = {}) {
      this.chart ??= window.echarts.init(this.$el)
      this.chart.setOption({ ...options, ...this.getDataOptions() })
      this.$dispatch('rendered', { chart: this.chart })
    },

    destroy() {
      _loadable.destroy.call(this)
      this.chart?.dispose()
      this.chart = null
    }
  }
}

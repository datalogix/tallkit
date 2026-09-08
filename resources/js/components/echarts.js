import { loadRemoteAssets } from '../utils'
import { dataOptions } from '../mixins/data-options'
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
      try {
        this.chart ??= window.echarts.init(this.$refs.target)
        this.chart.setOption({ ...options, ...this.getDataOptions(this.$refs.target) })
        this.$dispatch('rendered', { chart: this.chart })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.chart?.dispose()
      this.chart = null
    }
  }
}

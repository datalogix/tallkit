import { loadable } from './loadable'

export function echarts() {
  return {
    ...loadable(),

    chart: null,

    async init() {
      this.load(async () => {
        if (!window.echarts) {
          await this.$tallkit.loadScript('https://cdn.jsdelivr.net/npm/echarts@6')
        }
      })
    },

    render(options = {}) {
      this.chart ??= window.echarts.init(this.$el)
      this.chart.setOption(options)
      this.$dispatch('rendered', { chart: this.chart })
    }
  }
}

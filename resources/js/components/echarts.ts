import { loadable } from './loadable'

export function echarts() {
  return {
    ...loadable(),

    chart: null,

    init() {
      this.load(async () => {
        if (!window.echarts) {
          await this.$tallkit.loadScript('https://cdn.jsdelivr.net/npm/echarts@6')
        }
      })
    },

    getDataOptions() {
      return window.Alpine.evaluate(this.$el, this.$el.getAttribute('data-options') || '{}')
    },

    render(options = {}) {
      this.chart ??= window.echarts.init(this.$el)
      this.chart.setOption({ ...options, ...this.getDataOptions() })
      this.$dispatch('rendered', { chart: this.chart })
    }
  }
}

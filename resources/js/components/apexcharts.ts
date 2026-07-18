import { loadable } from './loadable'

export function apexcharts() {
  return {
    ...loadable(),

    chart: null,

    init() {
      this.load(async () => {
        if (!window.ApexCharts) {
          await this.$tallkit.loadScript('https://cdn.jsdelivr.net/npm/apexcharts@5')
        }
      })
    },

    getDataOptions() {
      return window.Alpine.evaluate(this.$el, this.$el.getAttribute('data-options') || '{}')
    },

    render(options = {}) {
      this.chart ??= new window.ApexCharts(this.$el, { ...options, ...this.getDataOptions() })
      this.chart.render()
      this.$dispatch('rendered', { chart: this.chart })
    }
  }
}

import { loadable } from './loadable'

export function apexcharts() {
  return {
    ...loadable(),

    chart: null,

    async init() {
      this.load(async () => {
        if (!window.ApexCharts) {
          await this.$tallkit.loadScript('https://cdn.jsdelivr.net/npm/apexcharts@5')
        }
      })
    },

    render(options = {}) {
      this.chart ??= new window.ApexCharts(this.$el, options)
      this.chart.render()
      this.$dispatch('rendered', { chart: this.chart })
    }
  }
}

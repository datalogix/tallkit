import { loadable } from './loadable'

export function chartjs() {
  return {
    ...loadable(),

    chart: null,

    async init() {
      this.load(async () => {
        if (!window.Chart) {
          await this.$tallkit.loadScript('https://cdn.jsdelivr.net/npm/chart.js@4')
        }
      })
    },

    render(options = {}) {
      this.chart ??= new window.Chart(this.$el, options)
      this.$dispatch('rendered', { chart: this.chart })
    }
  }
}

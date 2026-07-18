import { loadable } from './loadable'

export function chartjs() {
  return {
    ...loadable(),

    chart: null,

    init() {
      this.load(async () => {
        if (!window.Chart) {
          await this.$tallkit.loadScript('https://cdn.jsdelivr.net/npm/chart.js@4')
        }
      })
    },

    getDataOptions() {
      return window.Alpine.evaluate(this.$el, this.$el.getAttribute('data-options') || '{}')
    },

    render(options = {}) {
      this.chart ??= new window.Chart(this.$el, { ...options, ...this.getDataOptions() })
      this.$dispatch('rendered', { chart: this.chart })
    }
  }
}

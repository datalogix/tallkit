import { loadable } from './loadable'

export function frappeCharts() {
  return {
    ...loadable(),

    chart: null,

    init() {
      this.load(async () => {
        if (!window.frappe?.Chart) {
          await this.$tallkit.loadScript('https://cdn.jsdelivr.net/npm/frappe-charts@1')
        }
      })
    },

    getDataOptions() {
      return window.Alpine.evaluate(this.$el, this.$el.getAttribute('data-options') || '{}')
    },

    render(options = {}) {
      this.chart ??= new window.frappe.Chart(this.$el, { ...options, ...this.getDataOptions() })
      this.$dispatch('rendered', { chart: this.chart })
    }
  }
}

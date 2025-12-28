import { loadable } from './loadable'

export function frappeCharts() {
  return {
    ...loadable(),

    chart: null,

    async init() {
      this.load(async () => {
        if (!window.frappe?.Chart) {
          await this.$tallkit.loadScript('https://cdn.jsdelivr.net/npm/frappe-charts@1')
        }
      })
    },

    render(options = {}) {
      this.chart ??= new window.frappe.Chart(this.$el, options)
      this.$dispatch('rendered', { chart: this.chart })
    }
  }
}

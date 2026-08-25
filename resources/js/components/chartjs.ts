import { loadRemoteAssets } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { loadable } from './loadable'

export function chartjs() {
  const _loadable = loadable()

  return {
    ..._loadable,
    ...dataOptions(),

    chart: null,

    init() {
      this.load(() => loadRemoteAssets(() => !!window.Chart, 'https://cdn.jsdelivr.net/npm/chart.js@4'))
    },

    render(options = {}) {
      try {
        const merged = { ...options, ...this.getDataOptions() }

        if (this.chart) {
          Object.assign(this.chart.config, merged)
          this.chart.update()
        } else {
          this.chart = new window.Chart(this.$refs.target, merged)
        }

        this.$dispatch('rendered', { chart: this.chart })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.chart?.destroy()
      this.chart = null
    }
  }
}

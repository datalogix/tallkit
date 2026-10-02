import { loadRemoteAssets, debounce, emit } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { serverOptions } from '../mixins/server-options'
import { loadable } from './loadable'

export function frappeCharts() {
  const _loadable = loadable()

  // Out of Alpine's reactive data: called through its proxy, the library breaks.
  let chart = null

  return {
    ..._loadable,
    ...dataOptions(),
    ...serverOptions(),

    _resizeObserver: null,

    getChart() {
      return chart
    },

    init() {
      this.load(() => loadRemoteAssets(() => !!window.frappe?.Chart, 'https://cdn.jsdelivr.net/npm/frappe-charts@1'))

      this.followServerOptions((next) => {
        if (this.isCompleted() && this.$refs.target) this.render(next)
      })
    },

    render(options = {}) {
      try {
        chart?.destroy?.()
        chart = new window.frappe.Chart(this.$refs.target, { ...options, ...this.getDataOptions(this.$refs.target) })

        // One redraw once the size settles: overlapping redraws throw (removeChild).
        if (chart.boundDrawFn) {
          chart.resizeObserver?.disconnect()
          window.removeEventListener('resize', chart.boundDrawFn)
          window.removeEventListener('orientationchange', chart.boundDrawFn)

          this._resizeObserver?.disconnect()
          this._resizeObserver = new ResizeObserver(debounce(() => {
            try {
              chart?.draw?.(true)
            } catch {
              requestAnimationFrame(() => { try { chart?.draw?.(true) } catch { /* left as it was */ } })
            }
          }, 100))
          this._resizeObserver.observe(this.$refs.target)
        }
        emit(this.$refs.target, 'rendered', { chart }, { later: true })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.stopFollowingServerOptions()
      this._resizeObserver?.disconnect()
      this._resizeObserver = null
      chart?.destroy?.()
      chart = null
    }
  }
}

import { loadRemoteAssets, isDarkMode, onColorSchemeChange, emit } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { serverOptions } from '../mixins/server-options'
import { loadable } from './loadable'

export function apexcharts() {
  const _loadable = loadable()

  // Out of Alpine's reactive data: called through its proxy, the library breaks.
  let chart = null
  let source = null

  return {
    ..._loadable,
    ...dataOptions(),
    ...serverOptions(),

    _fixedMode: false,
    _palette: 'palette1',
    _stopColorScheme: null,

    getChart() {
      return chart
    },

    init() {
      this.load(() => loadRemoteAssets(() => !!window.ApexCharts, 'https://cdn.jsdelivr.net/npm/apexcharts@5'))

      this.followServerOptions((next) => {
        if (this.isCompleted() && this.$refs.target) this.render(next)
      })

      // Drawn again from what it was given: updating only its mode would bring in the dark palette's colors.
      this._stopColorScheme = onColorSchemeChange(() => {
        if (!chart || this._fixedMode || !source) return

        chart.destroy()
        chart = null
        this.render(source)
      })
    },

    render(options = {}) {
      try {
        source = { ...source, ...options }

        const merged = { ...options, ...this.getDataOptions(this.$refs.target) }

        this._fixedMode ||= !!merged.theme?.mode

        if (!this._fixedMode) {
          this._palette = merged.theme?.palette ?? this._palette
          merged.theme = { ...merged.theme, palette: this._palette, mode: isDarkMode() ? 'dark' : 'light' }
          merged.chart = { background: 'transparent', ...merged.chart }
        }

        if (chart) {
          chart.updateOptions(merged)
        } else {
          chart = new window.ApexCharts(this.$refs.target, merged)
          chart.render()
        }

        emit(this.$refs.target, 'rendered', { chart }, { later: true })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.stopFollowingServerOptions()
      this._stopColorScheme?.()
      chart?.destroy()
      chart = null
      source = null
    }
  }
}

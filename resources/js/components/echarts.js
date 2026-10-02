import { loadRemoteAssets, debounce, isDarkMode, onColorSchemeChange, emit } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { serverOptions } from '../mixins/server-options'
import { loadable } from './loadable'

export function echarts() {
  const _loadable = loadable()

  // Out of Alpine's reactive data: called through its proxy, the library breaks.
  let chart = null
  let source = null

  return {
    ..._loadable,
    ...dataOptions(),
    ...serverOptions(),

    _resizeObserver: null,
    _stopColorScheme: null,

    getChart() {
      return chart
    },

    init() {
      this.load(() => loadRemoteAssets(() => !!window.echarts, 'https://cdn.jsdelivr.net/npm/echarts@6'))

      this.followServerOptions((next) => {
        if (this.isCompleted() && this.$refs.target) this.render(next)
      })

      this._stopColorScheme = onColorSchemeChange(() => {
        if (!chart || !source) return

        this._resizeObserver?.disconnect()
        this._resizeObserver = null
        chart.dispose()
        chart = null
        this.render(source)
      })
    },

    render(options = {}) {
      try {
        source = { ...source, ...options }

        if (!chart) {
          chart = window.echarts.init(this.$refs.target, isDarkMode() ? 'dark' : null)

          if (isDarkMode()) chart.setOption({ backgroundColor: 'transparent' })

          const resize = debounce(() => chart?.resize(), 100)
          this._resizeObserver = new ResizeObserver(resize)
          this._resizeObserver.observe(this.$refs.target)
        }

        const option = { ...options, ...this.getDataOptions(this.$refs.target) }

        // Series replaced, not merged: fewer series would leave old ones drawn.
        chart.setOption(option, 'series' in option ? { replaceMerge: ['series'] } : {})
        emit(this.$refs.target, 'rendered', { chart }, { later: true })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.stopFollowingServerOptions()
      this._stopColorScheme?.()
      this._resizeObserver?.disconnect()
      this._resizeObserver = null
      chart?.dispose()
      chart = null
      source = null
    }
  }
}

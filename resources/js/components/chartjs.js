import { loadRemoteAssets, isDarkMode, onColorSchemeChange, emit } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { serverOptions } from '../mixins/server-options'
import { loadable } from './loadable'

const TEXT = { light: '#666', dark: 'rgba(255,255,255,0.7)' }
const GRID = { light: 'rgba(0,0,0,0.1)', dark: 'rgba(255,255,255,0.1)' }

function applyColorScheme(dark) {
  const defaults = window.Chart?.defaults
  if (!defaults) return

  if (Object.values(TEXT).includes(defaults.color)) defaults.color = dark ? TEXT.dark : TEXT.light

  const grid = defaults.scale?.grid
  if (grid && Object.values(GRID).includes(grid.color)) grid.color = dark ? GRID.dark : GRID.light
}

const copy = (value) => {
  if (Array.isArray(value)) return value.map(copy)
  if (value && Object.getPrototypeOf(value) === Object.prototype) {
    return Object.fromEntries(Object.entries(value).map(([k, v]) => [k, copy(v)]))
  }

  return value
}

export function chartjs() {
  const _loadable = loadable()

  // Out of Alpine's reactive data: called through its proxy, the library breaks.
  let chart = null
  // A copy: Chart.js writes its defaults into the config it gets.
  let source = null

  return {
    ..._loadable,
    ...dataOptions(),
    ...serverOptions(),

    _stopColorScheme: null,

    getChart() {
      return chart
    },

    init() {
      this.load(() => loadRemoteAssets(() => !!window.Chart, 'https://cdn.jsdelivr.net/npm/chart.js@4'))

      this.followServerOptions((next) => {
        if (this.isCompleted() && this.$refs.target) this.render(next)
      })

      this._stopColorScheme = onColorSchemeChange((dark) => {
        applyColorScheme(dark)

        if (!chart) return

        // Drawn again: an update keeps the colors its scales have already taken.
        chart.destroy()
        chart = new window.Chart(this.$refs.target, copy(source))
        emit(this.$refs.target, 'rendered', { chart }, { later: true })
      })
    },

    render(options = {}) {
      try {
        applyColorScheme(isDarkMode())

        const merged = { ...options, ...this.getDataOptions(this.$refs.target) }

        source = { ...source, ...merged }

        if (chart && !('plugins' in merged)) {
          // Not plugins: it has only a getter, and Object.assign throws.
          for (const key of ['type', 'data', 'options']) {
            if (key in merged) chart.config[key] = copy(merged[key])
          }

          chart.update()
        } else {
          chart?.destroy()
          chart = new window.Chart(this.$refs.target, copy(source))
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

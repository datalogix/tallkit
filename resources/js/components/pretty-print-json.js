import { loadRemoteAssets, escapeHtml, isDarkMode, onColorSchemeChange } from '../utils'
import { loadable } from './loadable'

export function prettyPrintJson() {
  const _loadable = loadable()

  return {
    ..._loadable,

    _stopColorScheme: null,

    init() {
      const syncScheme = (dark) => this.$root.classList.toggle('dark-mode', dark)
      syncScheme(isDarkMode())
      this._stopColorScheme = onColorSchemeChange(syncScheme)

      this.load(() => loadRemoteAssets(
        () => !!window.prettyPrintJson,
        'https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/pretty-print-json.min.js',
        'https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/css/pretty-print-json.min.css'
      ))
    },

    destroy() {
      _loadable.destroy.call(this)
      this._stopColorScheme?.()
    },

    render(data = null, options = null) {
      try {
        if (typeof data === 'string') {
          data = JSON.parse(data)
        }

        return window.prettyPrintJson.toHtml(data, options || {})
      } catch (e) {
        return escapeHtml(typeof data === 'string' ? data : JSON.stringify(data, null, 2)) ?? ''
      }
    },
  }
}

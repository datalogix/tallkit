import { loadRemoteAssets, escapeHtml } from '../utils'
import { loadable } from './loadable'

export function prettyPrintJson() {
  return {
    ...loadable(),

    init () {
      this.load(() => loadRemoteAssets(
        () => !!window.prettyPrintJson,
        'https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/pretty-print-json.min.js',
        'https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/css/pretty-print-json.min.css'
      ))
    },

    render (data = null, options = null) {
      try {
        if (typeof data === 'string') {
          data = JSON.parse(data)
        }

        return window.prettyPrintJson.toHtml(data, options || {})
      } catch(e) {
        return escapeHtml(typeof data === 'string' ? data : JSON.stringify(data, null, 2)) ?? ''
      }
    },
  }
}

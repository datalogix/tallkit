import { loadRemoteAssets } from '../utils'
import { loadable } from './loadable'

export function prettyPrintJson () {
  return {
    ...loadable(),

    init () {
      this.load(() => loadRemoteAssets(
        () => !!window.prettyPrintJson,
        'https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/pretty-print-json.min.js',
        'https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/css/pretty-print-json.min.css'
      ))
    },

    render (data = null, options = {}) {
      try {
        return window.prettyPrintJson.toHtml(data, options)
      } catch(e) {
        this.fail(e)

        return ''
      }
    },
  }
}

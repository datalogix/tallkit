import { loadable } from './loadable'

export function prettyPrintJson () {
  return {
    ...loadable(),

    init () {
      this.load(async () => {
        if (!window.prettyPrintJson) {
          await this.$tallkit.loadScript('https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/pretty-print-json.min.js')
          await this.$tallkit.loadStyle('https://cdn.jsdelivr.net/npm/pretty-print-json@3/dist/css/pretty-print-json.min.css')
        }
      })
    },

    render (data = null, options = {}) {
      try {
        return window.prettyPrintJson.toHtml(data, options)
      } catch(e) {
        this.fail(e)
      }
    },
  }
}

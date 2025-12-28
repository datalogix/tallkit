import { loadable } from './loadable'

export function highlightjs () {
  return {
    ...loadable(),

    init () {
      this.load(async () => {
        if (!window.hljs) {
          await this.$tallkit.loadScript('https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/highlight.min.js')
          await this.$tallkit.loadStyle('https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/styles/default.min.css')
        }
      })
    },

    render(code, language = null) {
      try {
        return language
          ? window.hljs.highlight(code, { language }).value
          : window.hljs.highlightAuto(code).value
      } catch(e) {
        this.fail(e)
      }
    },
  }
}

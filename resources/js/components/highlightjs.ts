import { loadRemoteAssets, escapeHtml } from '../utils'
import { loadable } from './loadable'

export function highlightjs () {
  return {
    ...loadable(),

    init () {
      this.load(() => loadRemoteAssets(
        () => !!window.hljs,
        'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/highlight.min.js',
        'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/styles/default.min.css'
      ))
    },

    render(code, language = null) {
      try {
        return language
          ? window.hljs.highlight(code, { language }).value
          : window.hljs.highlightAuto(code).value
      } catch(e) {
        this.fail(e)

        return escapeHtml(code) ?? ''
      }
    },
  }
}

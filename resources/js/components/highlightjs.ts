import { loadRemoteAssets, escapeHtml } from '../utils'
import { loadable } from './loadable'

export function highlightjs () {
  return {
    ...loadable(),

    language: null,

    init () {
      this.load(() => loadRemoteAssets(
        () => !!window.hljs,
        'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/highlight.min.js',
        'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/styles/default.min.css'
      ))
    },

    render(code, language = null) {
      try {
        const result = language
          ? window.hljs.highlight(code, { language })
          : window.hljs.highlightAuto(code)

        this.language = result.language ?? null

        return result.value
      } catch(e) {
        return escapeHtml(code) ?? ''
      }
    },
  }
}

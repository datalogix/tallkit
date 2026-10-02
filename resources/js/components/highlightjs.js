import { dataKey, dataSelector, loadRemoteAssets, escapeHtml, isDarkMode, onColorSchemeChange } from '../utils'
import { loadable } from './loadable'

const CDN = 'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build'

const THEMES = {
  light: `${CDN}/styles/github.min.css`,
  dark: `${CDN}/styles/github-dark.min.css`,
}

let followingTheme = false

function syncTheme() {
  const href = isDarkMode() ? THEMES.dark : THEMES.light
  let link = document.querySelector(`link${dataSelector('highlightjs-theme')}`)

  if (!link) {
    link = document.createElement('link')
    link.rel = 'stylesheet'
    link.setAttribute(dataKey('highlightjs-theme'), '')
    document.head.appendChild(link)
  }

  if (link.getAttribute('href') !== href) link.setAttribute('href', href)
}

function followTheme() {
  syncTheme()

  if (followingTheme) return

  followingTheme = true
  onColorSchemeChange(syncTheme)
}

export function highlightjs() {
  return {
    ...loadable(),

    language: null,

    init() {
      this.load(() => loadRemoteAssets(
        () => !!window.hljs,
        `${CDN}/highlight.min.js`,
      ).then(() => followTheme()))
    },

    render(code, language = null) {
      try {
        const result = language
          ? window.hljs.highlight(code, { language })
          : window.hljs.highlightAuto(code)

        this.language = result.language ?? null

        return result.value
      } catch (e) {
        return escapeHtml(code) ?? ''
      }
    },
  }
}

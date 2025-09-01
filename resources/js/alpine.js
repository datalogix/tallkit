import * as components from './components'
import { loadScript } from './utils'

async function loadAlpine () {
  if (window.Alpine) {
    return
  }

  await loadScript([
    'https://unpkg.com/@alpinejs/resize@3.x.x/dist/cdn.min.js',
    'https://unpkg.com/@alpinejs/mask@3.x.x/dist/cdn.min.js',
    'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js',
  ])
}

export function initAlpine () {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadAlpine)
  } else {
    loadAlpine()
  }
}

export function setupAlpine () {
  if (!window.Alpine) {
    return
  }

  Object.entries(components).forEach(([name, component]) => {
    window.Alpine.data(name, component)
  })

  window.Alpine.magic('tallkit', () => window.tallkit)
  window.Alpine.magic('tk', () => window.tallkit)
}

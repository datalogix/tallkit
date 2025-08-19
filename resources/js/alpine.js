import * as components from './components'

function loadScript(url) {
  return new Promise((resolve, reject) => {
    const script = document.createElement('script')
    script.src = url
    script.defer = true
    script.onload = resolve
    script.onerror = reject
    document.head.appendChild(script)
  })
}

async function loadAlpine () {
  if (window.Alpine) {
    return
  }

  await loadScript('https://unpkg.com/@alpinejs/resize@3.x.x/dist/cdn.min.js')
  await loadScript('https://unpkg.com/@alpinejs/mask@3.x.x/dist/cdn.min.js')
  await loadScript('https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js')
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

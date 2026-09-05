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

export function setupAlpine (tallkit) {
  const Alpine = window.Alpine

  if (!Alpine) {
    return
  }

  registerAlpineComponents()

  Alpine.store('tallkit', tallkit)
  Alpine.magic('tallkit', () => tallkit)
  Alpine.magic('tk', () => tallkit)
}

export function registerAlpineComponents() {
  const components = Object.fromEntries(
    Object.values(import.meta.glob('./components/*.js', { eager: true }))
      .flatMap(module =>
        Object.entries(module).filter(([, v]) => typeof v === 'function')
      )
  )

  for (const [name, fn] of Object.entries(components)) {
    window.Alpine.data(name, fn)
  }
}

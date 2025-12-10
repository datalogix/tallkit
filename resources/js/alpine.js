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

  registerAlpineComponents()

  window.Alpine.store('tallkit', tallkit)
  window.Alpine.magic('tallkit', () => Alpine.store('tallkit'))
  window.Alpine.magic('tk', () => Alpine.store('tallkit'))
}

export function registerAlpineComponents() {
  const components = Object.fromEntries(
    Object.values(import.meta.glob('./components/*.{js,ts}', { eager: true }))
      .flatMap(module =>
        Object.entries(module).filter(([, v]) => typeof v === 'function')
      )
  )

  for (const [name, fn] of Object.entries(components)) {
    window.Alpine.data(name, fn);
  }
}

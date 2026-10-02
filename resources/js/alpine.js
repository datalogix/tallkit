import { loadScript, syncIgnoredFieldState, guardFormResubmit, guardLoadingButtons } from './utils'
import { installTooltips, tooltipDirective } from './tooltip'

const ALPINE_VERSION = '3.17.4'
const ALPINE_URL = (name) => `https://unpkg.com/${name}@${ALPINE_VERSION}/dist/cdn.min.js`
const ALPINE_PLUGINS = ['@alpinejs/collapse', '@alpinejs/focus', '@alpinejs/persist', '@alpinejs/resize', '@alpinejs/mask', '@alpinejs/sort']

// Plugins in parallel: each one only waits for alpine:init. The core last, as it fires it.
function loadAlpine() {
  return Promise.all(ALPINE_PLUGINS.map((name) => loadScript(ALPINE_URL(name)))).then(() => loadScript(ALPINE_URL('alpinejs')))
}

const installed = new WeakSet()

export function install(Alpine, tallkit) {
  if (!Alpine || installed.has(Alpine)) {
    return
  }

  installed.add(Alpine)

  registerAlpineComponents(Alpine)

  syncIgnoredFieldState()

  guardFormResubmit()

  guardLoadingButtons()

  installTooltips()
  tooltipDirective(Alpine)

  // Replaces the object itself, so window.tallkit hands out the same reactive one.
  tallkit.appearance = Alpine.reactive(tallkit.appearance)

  Alpine.store('tallkit', tallkit)
  Alpine.magic('tallkit', () => tallkit)
  Alpine.magic('tk', () => tallkit)
}

export function bootAlpine(tallkit, { load = true } = {}) {
  let started = false

  document.addEventListener('alpine:init', () => {
    started = true

    if (window.Alpine) {
      install(window.Alpine, tallkit)
      return
    }

    console.warn('[tallkit] Alpine started without `window.Alpine`: register tallkit with `Alpine.plugin(tallkit)` (dist/tallkit.esm.js).')
  })

  if (window.Alpine) {
    warnIfAlreadyStarted()
    install(window.Alpine, tallkit)
    return
  }

  onReady(() => {
    if (window.Alpine || started) {
      return
    }

    if (!load) {
      console.warn('[tallkit] No Alpine found on the page, and loading it is turned off (tallkit.load_alpine).')
      return
    }

    loadAlpine().catch((e) => console.error('[tallkit] Alpine could not be loaded.', e))
  })
}

export function registerAlpineComponents(Alpine = window.Alpine) {
  const components = Object.fromEntries(
    Object.values(import.meta.glob('./components/*.js', { eager: true }))
      .flatMap(module =>
        Object.entries(module).filter(([, v]) => typeof v === 'function')
      )
  )

  for (const [name, fn] of Object.entries(components)) {
    Alpine.data(name, fn)
  }
}

function warnIfAlreadyStarted() {
  if (Array.from(document.querySelectorAll('[x-data]')).some((el) => el._x_dataStack)) {
    console.warn('[tallkit] Alpine had already started when tallkit loaded: load tallkit.js before Alpine (or Livewire).')
  }
}

function onReady(callback) {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', callback, { once: true })
  } else {
    callback()
  }
}

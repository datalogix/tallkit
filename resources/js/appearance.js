import { storageKey, getStoredText, setStoredText, removeStored, prefersReducedMotion } from './utils'

const STORAGE_KEY = storageKey('appearance')
export const MODES = ['system', 'light', 'dark']

const toMode = (value) => (MODES.includes(value) ? value : 'system')

export const appearance = {
  mode: toMode(getStoredText(STORAGE_KEY)),

  init() {
    this.apply(this.mode)

    document.addEventListener('livewire:navigated', () => this.apply(this.mode))

    window.addEventListener('storage', (event) => {
      if (event.key === STORAGE_KEY || event.key === null) window.tallkit.appearance.apply(toMode(event.newValue))
    })

    const media = window.matchMedia('(prefers-color-scheme: dark)')
    media.addEventListener('change', () => {
      if (this.mode === 'system') {
        this.apply('system')
      }
    })
  },

  isDark() {
    return document.documentElement.classList.contains('dark')
  },

  isLight() {
    return !this.isDark()
  },

  applyDark(persist = true) {
    document.documentElement.classList.add('dark')
    if (persist) setStoredText(STORAGE_KEY, 'dark')
    this.mode = 'dark'
  },

  applyLight(persist = true) {
    document.documentElement.classList.remove('dark')
    if (persist) setStoredText(STORAGE_KEY, 'light')
    this.mode = 'light'
  },

  apply(appearance) {
    if (appearance === 'system') {
      const media = window.matchMedia('(prefers-color-scheme: dark)')
      removeStored(STORAGE_KEY)
      if (media.matches) {
        this.applyDark(false)
      } else {
        this.applyLight(false)
      }
      this.mode = 'system'
    } else if (appearance === 'dark') {
      this.applyDark()
    } else if (appearance === 'light') {
      this.applyLight()
    }
  },

  toggle(event, options = {}) {
    const isAppearanceTransition = typeof document !== 'undefined'
      && typeof document.startViewTransition === 'function'
      && !prefersReducedMotion()

    if (!isAppearanceTransition || !event) {
      return this.isDark() ? this.applyLight() : this.applyDark()
    }

    const transition = document.startViewTransition(() => this.isDark() ? this.applyLight() : this.applyDark())
    const rect = event.currentTarget?.getBoundingClientRect?.()
    const fromKeyboard = !event.clientX && !event.clientY && rect
    const x = fromKeyboard ? rect.left + rect.width / 2 : event.clientX || 0
    const y = fromKeyboard ? rect.top + rect.height / 2 : event.clientY || 0
    const endRadius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y))

    transition.ready.then(() => {
      const clipPath = [
        `circle(0px at ${x}px ${y}px)`,
        `circle(${endRadius}px at ${x}px ${y}px)`,
      ]
      document.documentElement.animate(
        {
          clipPath: this.isDark()
            ? [...clipPath].reverse()
            : clipPath,
        },
        {
          duration: 300,
          easing: 'ease-in',
          ...(options || {}),
          pseudoElement: this.isDark()
            ? '::view-transition-old(root)'
            : '::view-transition-new(root)',
        },
      )
    })
  }
}

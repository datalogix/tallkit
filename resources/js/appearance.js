export const appearance = {
  mode: window.localStorage.getItem('tallkit.appearance') || 'system',

  init() {
    this.apply(this.mode)
    document.addEventListener('livewire:navigated', () => this.apply(this.mode))

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

  applyDark(storage = true) {
    document.documentElement.classList.add('dark')
    if (storage) window.localStorage.setItem('tallkit.appearance', 'dark')
    this.mode = 'dark'
  },

  applyLight(storage = true) {
    document.documentElement.classList.remove('dark')
    if (storage) window.localStorage.setItem('tallkit.appearance', 'light')
    this.mode = 'light'
  },

  apply(appearance) {
    if (appearance === 'system') {
      const media = window.matchMedia('(prefers-color-scheme: dark)')
      window.localStorage.removeItem('tallkit.appearance')
      media.matches ? this.applyDark(false) : this.applyLight(false)
      this.mode = 'system'
    } else if (appearance === 'dark') {
      this.applyDark()
    } else if (appearance === 'light') {
      this.applyLight()
    }
  },

  toggle(event, options = {}) {
    const isAppearanceTransition = typeof document !== 'undefined'
      && document.startViewTransition
      && !window.matchMedia('(prefers-reduced-motion: reduce)').matches

    if (!isAppearanceTransition || !event) {
      return this.isDark() ? this.applyLight() : this.applyDark()
    }

    const transition = document.startViewTransition(() => this.isDark() ? this.applyLight() : this.applyDark())
    const x = event.clientX || 0
    const y = event.clientY || 0
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

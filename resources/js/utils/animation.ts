import { getTransitionTimeout } from './transition'

type Options = {
  from?: string[]
  to?: string[]
  start?: () => void
  finish?: () => void
  onDone?: () => void
  remove?: boolean
}

type Cleanup = () => void

export function animation(el: HTMLElement, options: Options = {}): Cleanup {
  let fallbackId: number | null = null
  let onTransitionEnd: ((e: TransitionEvent) => void) | null = null
  let finished = false

  const cleanup: Cleanup = () => {
    if (fallbackId !== null) {
      clearTimeout(fallbackId)
      fallbackId = null
    }

    if (onTransitionEnd) {
      el.removeEventListener('transitionend', onTransitionEnd)
      onTransitionEnd = null
    }
  }

  const finish = () => {
    if (finished) return
    finished = true

    cleanup()

    if (options.remove && el.isConnected) {
      el.remove()
    }

    options.onDone?.()
  }

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches

  const applyClasses = (remove: string[] = [], add: string[] = []) => {
    if (remove.length) el.classList.remove(...remove)
    if (add.length) el.classList.add(...add)
  }

  if (reduceMotion) {
    applyClasses(options.from, options.to)
    options.start?.()
    options.finish?.()
    finish()
    return () => {}
  }

  applyClasses(options.to, options.from)
  options.start?.()

  requestAnimationFrame(() => {
    void el.offsetHeight

    applyClasses(options.from, options.to)
    options.finish?.()
  })

  onTransitionEnd = (event: TransitionEvent) => {
    if (event.target !== el) return

    finish()
  }

  el.addEventListener('transitionend', onTransitionEnd)

  const timeout = getTransitionTimeout(el)

  if (timeout === 0) {
    finish()
  } else {
    const fallbackDelay = Math.max(timeout * 0.1, 50)
    fallbackId = window.setTimeout(finish, timeout + fallbackDelay)
  }

  return cleanup
}

export function fadeOut(el: HTMLElement, options: Options = {}) {
  return animation(el, {
    from: ['opacity-100'],
    to: ['opacity-0'],
    remove: true,
    ...options
  })
}

export function fadeIn(el: HTMLElement, options: Options = {}) {
  if (!el.classList.contains('opacity-0')) {
    el.classList.add('opacity-0')
  }

  return animation(el, {
    from: ['opacity-0'],
    to: ['opacity-100'],
    ...options
  })
}

export function collapse(el: HTMLElement, options: Options = {}) {
  const style = window.getComputedStyle(el)

  const height = el.offsetHeight

  const marginTop = style.marginTop
  const marginBottom = style.marginBottom
  const paddingTop = style.paddingTop
  const paddingBottom = style.paddingBottom

  el.style.height = `${height}px`
  el.style.overflow = 'hidden'
  el.style.marginTop = marginTop
  el.style.marginBottom = marginBottom
  el.style.paddingTop = paddingTop
  el.style.paddingBottom = paddingBottom
  el.style.opacity = '1'

  void el.offsetHeight

  return animation(el, {
    ...options,

    start() {
      el.style.willChange = 'height, margin, padding, opacity'

      options.start?.()
    },

    finish() {
      el.style.height = '0px'
      el.style.marginTop = '0px'
      el.style.marginBottom = '0px'
      el.style.paddingTop = '0px'
      el.style.paddingBottom = '0px'
      el.style.opacity = '0'

      options.finish?.()
    },

    onDone() {
      el.style.removeProperty('height')
      el.style.removeProperty('overflow')
      el.style.removeProperty('margin-top')
      el.style.removeProperty('margin-bottom')
      el.style.removeProperty('padding-top')
      el.style.removeProperty('padding-bottom')
      el.style.removeProperty('opacity')
      el.style.removeProperty('will-change')

      options.onDone?.()
    },
  })
}

export function expand(el: HTMLElement, options: Options = {}) {
  const style = window.getComputedStyle(el)

  const targetHeight = el.scrollHeight

  const marginTop = style.marginTop
  const marginBottom = style.marginBottom
  const paddingTop = style.paddingTop
  const paddingBottom = style.paddingBottom

  el.style.height = '0px'
  el.style.overflow = 'hidden'
  el.style.marginTop = '0px'
  el.style.marginBottom = '0px'
  el.style.paddingTop = '0px'
  el.style.paddingBottom = '0px'
  el.style.opacity = '0'

  void el.offsetHeight

  return animation(el, {
    ...options,

    start() {
      el.style.willChange = 'height, margin, padding, opacity'
      options.start?.()
    },

    finish() {
      el.style.height = `${targetHeight}px`
      el.style.marginTop = marginTop
      el.style.marginBottom = marginBottom
      el.style.paddingTop = paddingTop
      el.style.paddingBottom = paddingBottom
      el.style.opacity = '1'

      options.finish?.()
    },

    onDone() {
      el.style.removeProperty('height')
      el.style.removeProperty('overflow')
      el.style.removeProperty('margin-top')
      el.style.removeProperty('margin-bottom')
      el.style.removeProperty('padding-top')
      el.style.removeProperty('padding-bottom')
      el.style.removeProperty('opacity')
      el.style.removeProperty('will-change')

      options.onDone?.()
    },
  })
}

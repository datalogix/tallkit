import { getTransitionTimeout } from './transition'

type CollapseAndRemoveOptions = {
  animated?: boolean
  onDone?: () => void
}

export function collapseAndRemove(root: HTMLElement, options: CollapseAndRemoveOptions = {}) {
  let fallbackId: number | null = null
  let onTransitionEnd: ((event: TransitionEvent) => void) | null = null
  let finished = false

  const cleanup = () => {
    if (fallbackId !== null) {
      clearTimeout(fallbackId)
      fallbackId = null
    }

    if (onTransitionEnd) {
      root.removeEventListener('transitionend', onTransitionEnd)
      onTransitionEnd = null
    }
  }

  const finish = () => {
    if (finished) {
      return
    }

    finished = true
    cleanup()

    if (root.isConnected) {
      root.remove()
    }

    options.onDone?.()
  }

  if (!options.animated || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    finish()
    return cleanup
  }

  const height = root.offsetHeight
  const style = window.getComputedStyle(root)

  root.style.height = `${height}px`
  root.style.overflow = 'hidden'
  root.style.willChange = 'height, opacity, margin-bottom, padding-top, padding-bottom'
  root.style.marginBottom = style.marginBottom
  root.style.paddingTop = style.paddingTop
  root.style.paddingBottom = style.paddingBottom

  root.getBoundingClientRect()

  requestAnimationFrame(() => {
    root.style.opacity = '0'
    root.style.height = '0px'
    root.style.marginBottom = '0px'
    root.style.paddingTop = '0px'
    root.style.paddingBottom = '0px'
  })

  onTransitionEnd = (event: TransitionEvent) => {
    if (event.target !== root) return
    if (event.propertyName !== 'height') return

    finish()
  }

  root.addEventListener('transitionend', onTransitionEnd)

  const transitionTimeout = getTransitionTimeout(root)

  if (transitionTimeout === 0) {
    finish()
  } else {
    fallbackId = window.setTimeout(() => finish(), transitionTimeout + 50)
  }

  return cleanup
}

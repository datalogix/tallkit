import { animation } from './animation'

type Phase = {
  from?: string[]
  to?: string[]
}

type TransitionOptions = {
  enter?: Phase
  leave?: Phase
  removeOnLeave?: boolean
}

export function transition(el: HTMLElement, options: TransitionOptions) {
  const enter = (opts = {}) => animation(el, {
    from: options.enter?.from,
    to: options.enter?.to,
    ...opts,
  })

  const leave = (opts = {}) => animation(el, {
    from: options.leave?.from,
    to: options.leave?.to,
    remove: options.removeOnLeave ?? true,
    ...opts,
  })

  return { enter, leave }
}

export function parseTimeToMilliseconds(value: string) {
  const parsed = Number.parseFloat(value)

  if (Number.isNaN(parsed)) {
    return 0
  }

  return value.trim().endsWith('ms') ? parsed : parsed * 1000
}

export function getTransitionTimeout(element: HTMLElement) {
  const style = window.getComputedStyle(element)
  const durations = style.transitionDuration.split(',')
  const delays = style.transitionDelay.split(',')

  return durations.reduce((max, duration, index) => {
    const delay = delays[index] ?? delays[delays.length - 1] ?? '0s'
    return Math.max(max, parseTimeToMilliseconds(duration) + parseTimeToMilliseconds(delay))
  }, 0)
}

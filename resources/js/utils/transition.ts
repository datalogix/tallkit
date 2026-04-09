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

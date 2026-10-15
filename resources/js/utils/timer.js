import { clamp, toNumber } from './number'

export function toMilliseconds(value, fallback = 0) {
  const parsed = toNumber(value)

  if (parsed === null) return fallback

  return Math.max(/\ds$/.test(String(value).trim()) ? parsed * 1000 : parsed, 0)
}

export function startTimeout(callback, milliseconds, defaultMilliseconds = 500) {
  return setTimeout(callback, toMilliseconds(milliseconds, defaultMilliseconds))
}

export function startInterval(callback, milliseconds, defaultMilliseconds = 500) {
  return setInterval(callback, toMilliseconds(milliseconds, defaultMilliseconds))
}

export function debounce(callback, delay = 300) {
  let timeout = undefined

  const debounced = (...args) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => callback(...args), delay)
  }

  debounced.cancel = () => clearTimeout(timeout)

  return debounced
}

export function readingDuration(title = '', message = '') {
  const text = `${title} ${message}`.trim()

  const min = 3000
  const max = 9000
  const base = 1000

  const weightedLength =
    (title?.length ?? 0) * 1.2 +
    (message?.length ?? 0) * 1.6

  const readingSpeed = 16

  let time = base + (weightedLength / readingSpeed) * 1000

  const lines = text.split('\n').length
  time += lines * 300

  return clamp(time, min, max)
}

import { toNumber } from './number'

// A bare number is milliseconds; a CSS time ('0.3s', '300ms') by its unit. 0 is a time too: only an unreadable value takes the fallback.
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

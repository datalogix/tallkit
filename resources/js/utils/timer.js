export function timeout(callback, milliseconds, defaultMilliseconds = 500) {
  const ms = !milliseconds || isNaN(parseInt(milliseconds.toString()))
    ? defaultMilliseconds
    : parseInt(milliseconds.toString())

  return setTimeout(callback, ms)
}

export function interval(callback, milliseconds, defaultMilliseconds = 500) {
  const ms = !milliseconds || isNaN(parseInt(milliseconds.toString()))
    ? defaultMilliseconds
    : parseInt(milliseconds.toString())

  return setInterval(callback, ms)
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

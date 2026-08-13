export type Milliseconds = string | boolean | number

export function timeout(callback: TimerHandler, milliseconds?: Milliseconds, defaultMilliseconds: number = 500) {
  const ms = !milliseconds || isNaN(parseInt(milliseconds.toString()))
    ? defaultMilliseconds
    : parseInt(milliseconds.toString())

  return setTimeout(callback, ms)
}

export function interval(callback: TimerHandler, milliseconds?: Milliseconds, defaultMilliseconds: number = 500) {
  const ms = !milliseconds || isNaN(parseInt(milliseconds.toString()))
    ? defaultMilliseconds
    : parseInt(milliseconds.toString())

  return setInterval(callback, ms)
}

export function debounce(callback: (...args: any[]) => void, delay: number = 300) {
  let timeout: number | undefined = undefined

  const debounced = (...args: any[]) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => callback(...args), delay)
  }

  debounced.cancel = () => clearTimeout(timeout)

  return debounced
}

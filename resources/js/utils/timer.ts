export type Milliseconds = string | boolean | number

export function timeout(callback: TimerHandler, milliseconds?: Milliseconds, defaultMilliseconds: number = 500) {
  let timeoutId: number | undefined = undefined
  clearTimeout(timeoutId)

  const ms = !milliseconds || isNaN(parseInt(milliseconds.toString()))
    ? defaultMilliseconds
    : parseInt(milliseconds.toString())

  timeoutId = setTimeout(callback, ms)
  return timeoutId
}

export function interval(callback: TimerHandler, milliseconds?: Milliseconds, defaultMilliseconds: number = 500) {
  let intervalId: number | undefined = undefined
  clearInterval(intervalId)

  const ms = !milliseconds || isNaN(parseInt(milliseconds.toString()))
    ? defaultMilliseconds
    : parseInt(milliseconds.toString())

  intervalId = setInterval(callback, ms)
  return intervalId
}

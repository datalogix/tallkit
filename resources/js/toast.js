export function toast(...args) {
  let detail

  if (typeof args[0] === 'object' && args[0] !== null && !Array.isArray(args[0])) {
    detail = args[0]
  } else {
    const [message, title, type, duration, position] = args
    detail = { message, title, type, duration, position }
  }

  document.dispatchEvent(new CustomEvent('toast', { detail }))
}

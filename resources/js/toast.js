export function toast(...args) {
  if (args.length === 0) {
    return {
      success: (...props) => toast({ ...parseArgs(...props), type: 'success' }),
      error: (...props) => toast({ ...parseArgs(...props), type: 'error' }),
      info: (...props) => toast({ ...parseArgs(...props), type: 'info' }),
      warning: (...props) => toast({ ...parseArgs(...props), type: 'warning' }),
    };
  }

  document.dispatchEvent(new CustomEvent('toast', { detail: parseArgs(...args) }))
}

const parseArgs = (...args) => {
  if (typeof args[0] === 'object' && args[0] !== null && !Array.isArray(args[0])) {
    return args[0]
  }

  const [message, title, type, duration, position, progress, size] = args
  return { message, title, type, duration, position, progress, size }
}

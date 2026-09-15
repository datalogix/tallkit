export function toast(...args) {
  if (args.length === 0) {
    return {
      success: (...props) => toast({ ...parseArgs(...props), type: 'success' }),
      error: (...props) => toast({ ...parseArgs(...props), type: 'error' }),
      info: (...props) => toast({ ...parseArgs(...props), type: 'info' }),
      warning: (...props) => toast({ ...parseArgs(...props), type: 'warning' }),
    };
  }

  emit('toast', parseArgs(...args))
}

export function closeToast(id) {
  emit('toast-close', { id })
}

function emit(event, detail) {
  if (window.__tallkitToastReady) {
    document.dispatchEvent(new CustomEvent(event, { detail }))
  } else {
    (window.__tallkitToastQueue ??= []).push({ event, detail })
  }
}

const parseArgs = (...args) => {
  if (typeof args[0] === 'object' && args[0] !== null && !Array.isArray(args[0])) {
    return args[0]
  }

  const [message, title, type, duration, position, progress, size, invert, actions, id] = args
  const detail = { message, title, type, duration, position, progress, size, invert, actions, id }

  return Object.fromEntries(Object.entries(detail).filter(([, value]) => value !== null))
}

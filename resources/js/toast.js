import { emit, eventName } from './utils'

export function toast(...args) {
  if (args.length === 0) {
    return {
      success: (...props) => toast({ ...parseArgs(...props), type: 'success' }),
      error: (...props) => toast({ ...parseArgs(...props), type: 'error' }),
      info: (...props) => toast({ ...parseArgs(...props), type: 'info' }),
      warning: (...props) => toast({ ...parseArgs(...props), type: 'warning' }),
      loading: (...props) => toast({ duration: false, progress: false, swipe: false, ...parseArgs(...props), type: 'loading' }),
      promise: (promise, messages = {}) => container()?.promise(promise, messages) ?? promise,
      close: (id) => sendToastEvent(eventName('toast-close'), { id }),
    }
  }

  sendToastEvent(eventName('toast'), parseArgs(...args))
}

const container = () => {
  const el = window.__tallkitToastReady ? window.__tallkitToastContainer : null

  return el?.isConnected && window.Alpine ? window.Alpine.$data(el) : null
}

export function sendToastEvent(event, detail) {
  if (window.__tallkitToastReady) {
    emit(document, event, detail)
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

  return Object.fromEntries(Object.entries(detail).filter(([, value]) => value !== null && value !== undefined))
}

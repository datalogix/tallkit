export function emit(el, name, detail = {}, { later = false, bubbles = false, cancelable = false } = {}) {
  if (!el) return null

  const event = new CustomEvent(name, { detail, bubbles, cancelable })

  if (later) {
    queueMicrotask(() => el.dispatchEvent(event))
    return null
  }

  el.dispatchEvent(event)

  return event
}

export function listenWhileConnected(root, target, type, handler) {
  if (!target) return () => {}

  const controller = new AbortController()

  target.addEventListener(type, handler, { signal: controller.signal })
  window.Alpine?.onElRemoved?.(root, () => controller.abort())

  return () => controller.abort()
}

export function onFormReset(root, form, callback) {
  return listenWhileConnected(root, form, 'reset', () => setTimeout(() => {
    if (root.isConnected) callback()
  }))
}

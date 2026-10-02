const handled = new WeakSet()

export function markEscapeHandled(event) {
  handled.add(event)
}

export function isEscapeHandled(event) {
  return handled.has(event)
}

const layers = []

function onEscape(event) {
  if (event.key !== 'Escape') return

  while (layers.length && layers[layers.length - 1].popoverElement?.isConnected === false) layers.pop()

  if (!layers.length) window.removeEventListener('keydown', onEscape, true)

  const top = layers[layers.length - 1]

  if (!top) return

  markEscapeHandled(event)

  const focusWasInside = top.popoverElement?.contains(document.activeElement)

  top.close()

  if (focusWasInside) top.ariaTrigger?.focus?.()
}

export function pushEscapeLayer(layer) {
  removeEscapeLayer(layer)
  layers.push(layer)

  if (layers.length === 1) window.addEventListener('keydown', onEscape, true)
}

export function removeEscapeLayer(layer) {
  const index = layers.indexOf(layer)

  if (index !== -1) layers.splice(index, 1)

  if (!layers.length) window.removeEventListener('keydown', onEscape, true)
}

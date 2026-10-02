export function bind(
  el,
  bindings
) {
  const elements = el instanceof Element ? [el] : el

  Array.from(elements ?? [])
    .filter(element => element instanceof Element)
    .forEach((element, index) => {
      window.Alpine.bind(element, (typeof bindings === 'function' ? bindings(element, index) : bindings))
    })
}

export function bindShortcut(el, shortcut, callback) {
  const typable = !String(shortcut).split('.').some((key) => ['ctrl', 'cmd', 'meta', 'alt'].includes(key))

  bind(el, {
    [`@keydown.${shortcut}.document`](event) {
      if (typable && isTypingIn(event.target)) return

      if (callback(event) === false) return

      event.preventDefault()
    }
  })
}

const NON_TEXT_INPUTS = ['checkbox', 'radio', 'button', 'submit', 'reset', 'range', 'color', 'file', 'image']

export function isTypingIn(target) {
  if (!(target instanceof Element)) return false

  if (target.isContentEditable || target.closest('[contenteditable]:not([contenteditable="false"])')) return true
  if (target.matches('textarea, select')) return true

  return target.matches('input') && !NON_TEXT_INPUTS.includes(target.type)
}

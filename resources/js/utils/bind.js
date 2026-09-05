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
  bind(el, {
    [`@keydown.${shortcut}.document`](event) {
      event.preventDefault()
      callback(event)
    }
  })
}

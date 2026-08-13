export function bind(
  el: null | Element | Element[] | NodeListOf<Element>,
  bindings: ((element: Element, index: number) => object | (() => object)) | object | (() => object)
) {
  const elements = el instanceof Element ? [el] : el

  Array.from(elements ?? [])
    .filter((element): element is Element => element instanceof Element)
    .forEach((element, index) => {
      window.Alpine.bind(element, typeof bindings === 'function' ? bindings(element, index) : bindings)
    })
}

export function bindShortcut(el: null | Element, shortcut: string, callback: (event: KeyboardEvent) => void) {
  bind(el, {
    [`@keydown.${shortcut}.document`](event: KeyboardEvent) {
      event.preventDefault()
      callback(event)
    }
  })
}

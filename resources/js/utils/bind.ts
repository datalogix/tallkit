export function bind(el: null | Element | Element[] | NodeListOf<Element>, bindings: object | (() => object)) {
  const elements = el instanceof Element ? [el] : el

  elements?.forEach(element => {
    window.Alpine.bind(element, bindings)
  })
}

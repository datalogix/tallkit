export function bind(
  el: null | Element | Element[] | NodeListOf<Element>,
  bindings: ((element: Element, index: number) => object | (() => object)) | object | (() => object)
) {
  const elements = el instanceof Element ? [el] : el

  elements?.forEach((element, index) => {
    window.Alpine.bind(element, typeof bindings === 'function' ? bindings(element, index) : bindings)
  })
}

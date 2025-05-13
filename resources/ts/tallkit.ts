import * as components from './components'

document.addEventListener('alpine:init', () => {
  const tallkit = {
    toast() {
      const detail = {
        text: '',
        heading: '',
      }

      if (arguments.length > 1) {
        detail.heading = arguments[0]
        detail.text = arguments[1]
      } else if (typeof arguments[0] === 'string') {
        detail.text = arguments[0]
      } else {
        detail.text = arguments[0].text
        detail.heading = arguments[0].heading
      }

      document.dispatchEvent(new CustomEvent("toast", { detail }))
    }
  }

  Object.entries(components).forEach(([name, component]) => {
    Alpine.data(name, component)
  })

  window.TALLKit = window.TK =tallkit
  Alpine.magic('tallkit', () => tallkit)
  Alpine.magic('tk', () => tallkit)
})

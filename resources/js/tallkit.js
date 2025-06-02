import * as components from './components'

/*
document.addEventListener('livewire:init', () => {
  window.Livewire.directive('success', ({ el, component }) => {
    window.Livewire.hook('commit', ({ component: iComponent, commit: payload, succeed }) => {
      if (iComponent !== component) return

      //if (targets.length > 0 && containsTargets(payload, targets) === inverted) return

      el.style.display = 'none'

      succeed(() => {
        const errors = Object.keys(component.snapshot.memo.errors)

        if (errors.length) {
          return
        }

        el.style.display = 'block'
      })
    })
  })
})
*/

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

  window.TALLKit = window.TK = tallkit
  Alpine.magic('tallkit', () => tallkit)
  Alpine.magic('tk', () => tallkit)
})

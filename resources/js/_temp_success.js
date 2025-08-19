
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


[wire\:success][wire\:success] {
  display: none !important;
}

*/

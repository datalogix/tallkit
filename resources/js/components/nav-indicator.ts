import { getTransitionTimeout } from '../utils'

export function navIndicator({ mode = null } = {}) {
  return {
    init () {
      document.addEventListener('livewire:navigated', this.move.bind(this))
      window.addEventListener('resize', this.move.bind(this))
    },

    move () {
      requestAnimationFrame(() => {
        const indicator = this.$el
        const nav = indicator.closest('[data-tallkit-nav]') ?? indicator.parentElement.previousElementSibling
        const link = nav?.querySelector('a[data-current]')

        if (!link) return

        const indicatorRect = indicator.getBoundingClientRect()
        const navRect = nav.getBoundingClientRect()
        const linkRect = link.getBoundingClientRect()
        const style = getComputedStyle(link)

        const x = link.offsetLeft + nav.offsetLeft
        const y = link.offsetTop + nav.offsetTop

        if (linkRect.width <= 0 || linkRect.height <= 0 || linkRect.top <= 0 || linkRect.left <= 0) {
          indicator.style.opacity = '0'
          return
        }

        indicator.style.opacity = '1'

        if (indicatorRect.width <= 0 || indicatorRect.height <= 0 || indicatorRect.top <= 0 || indicatorRect.left <= 0) {
          indicator.style.visibility = 'hidden'

          setTimeout(() => {
            indicator.style.visibility = 'visible'
          }, getTransitionTimeout(indicator))
        }

        if (mode === 'line-left' || mode === 'line-right') {
          indicator.style.height = `${linkRect.height}px`
          indicator.style.transform = `translate(${x + (mode === 'line-left' ? -10 : linkRect.width + 10)}px, ${y}px)`
          return
        }

        if (mode === 'line-top' || mode === 'line-bottom') {
          indicator.style.width = `${linkRect.width}px`
          indicator.style.transform = `translate(${x}px, ${y + (mode === 'line-top' ? -10 : linkRect.height + 10)}px)`
          return
        }

        // bg
        indicator.style.transform = `translate(${x}px, ${y}px)`
        indicator.style.width = `${linkRect.width}px`
        indicator.style.height = `${linkRect.height}px`
        indicator.style.borderRadius = style.borderRadius
      })
    }
  }
}

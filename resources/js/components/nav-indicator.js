import { dataKey, getTransitionTimeout } from '../utils'

export function navIndicator({ mode = null } = {}) {
  return {
    _visibilityTimeout: null,

    init () {
      this._onMove = this.move.bind(this)

      document.addEventListener('livewire:navigated', this._onMove)
      window.addEventListener('resize', this._onMove)

      this.$nextTick(() => this.move())
    },

    destroy () {
      document.removeEventListener('livewire:navigated', this._onMove)
      window.removeEventListener('resize', this._onMove)
      clearTimeout(this._visibilityTimeout)
    },

    findNav(el) {
      let node = el

      while (node) {
        const sibling = node.previousElementSibling

        if (sibling?.matches(dataKey('nav'))) {
          return sibling
        }

        node = node.parentElement
      }

      return null
    },

    move () {
      requestAnimationFrame(() => {
        const indicator = this.$el
        const nav = this.findNav(indicator)
        const link = nav?.querySelector('a[data-current]')

        if (!link) return

        const indicatorRect = indicator.getBoundingClientRect()
        const linkRect = link.getBoundingClientRect()

        const x = link.offsetLeft + nav.offsetLeft
        const y = link.offsetTop + nav.offsetTop

        if (linkRect.width <= 0 || linkRect.height <= 0) {
          indicator.style.opacity = '0'
          return
        }

        indicator.style.opacity = '1'

        if (indicatorRect.width <= 0 || indicatorRect.height <= 0 || indicatorRect.top <= 0 || indicatorRect.left <= 0) {
          indicator.style.visibility = 'hidden'

          clearTimeout(this._visibilityTimeout)
          this._visibilityTimeout = setTimeout(() => {
            indicator.style.visibility = 'visible'
            this._visibilityTimeout = null
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

        const style = getComputedStyle(link)
        indicator.style.transform = `translate(${x}px, ${y}px)`
        indicator.style.width = `${linkRect.width}px`
        indicator.style.height = `${linkRect.height}px`
        indicator.style.borderRadius = style.borderRadius
      })
    }
  };
}

import { bind } from '../utils'

export function slider() {
  return {
    get input () {
      return this.$root.querySelector('[data-tallkit-control]')
    },

    init() {
      this.updateRange()

      bind(this.input, {
        ['@input']: () => this.updateRange()
      })

      bind(this.$root.querySelector('[data-tallkit-slider-ticks]'), {
        ['@click']: (e) => {
          const ticks = [...this.$root.querySelectorAll('[data-tallkit-slider-tick]')]
          const clickX = e.clientX

          let closestTick = null
          let minDistance = Infinity

          ticks.forEach(tick => {
            const rect = tick.getBoundingClientRect()
            const centerX = rect.left + rect.width / 2
            const distance = Math.abs(clickX - centerX)

            if (distance < minDistance) {
              minDistance = distance
              closestTick = tick
            }
          })

          if (closestTick) {
            this.setValue(closestTick.getAttribute('value'))
          }
        }
      })
    },

    setValue(value) {
      if (this.input.disabled) return

      this.input.value = value;
      this.input.dispatchEvent(new Event('input', { bubbles: true }))
    },

    updateRange() {
      const min = Number(this.input.min || 0)
      const max = Number(this.input.max || 100)
      const val = Number(this.input.value)
      const p = ((val - min) * 100) / (max - min)
      this.input.style.setProperty('--range-percent', `${p}%`)
    }
  }
}

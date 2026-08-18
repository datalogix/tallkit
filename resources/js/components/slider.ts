import { dataKey, bind, getWireModelInfo, setFieldValue } from '../utils'

export function slider() {
  return {
    input: null,

    init() {
      this.input = this.$root.querySelector(dataKey('control'))
      this.$nextTick(() => this.updateRange())

      if (this.$wire) {
        const prop = getWireModelInfo(this.input)

        if (prop) {
          this.$wire.$watch(prop.name, () => this.updateRange())
        }
      }

      bind(this.input, {
        ['@input']: () => this.updateRange()
      })

      bind(this.$root.querySelector(dataKey('slider-ticks')), {
        ['@click']: (e) => {
          const ticks = [...this.$root.querySelectorAll(dataKey('slider-tick'))]
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
            let value = parseInt(closestTick.getAttribute('data-value'))

            if (isNaN(value)) {
              value = parseInt(closestTick.textContent.trim())
            }

            if (! isNaN(value)) {
              this.setValue(value)
            }
          }
        }
      })
    },

    setValue(value) {
      if (this.input.disabled) return

      setFieldValue(this.input, value)
    },

    updateRange() {
      const min = Number(this.input.min || 0)
      const max = Number(this.input.max || 100)
      const val = Number(this.input.value)
      const p = max === min ? 0 : ((val - min) * 100) / (max - min)

      this.input.style.setProperty('--range-percent', `${p}%`)
      this.input.classList.toggle('before:rounded-r-none', p < 50)
    }
  }
}

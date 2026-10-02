import { queryData, queryAllData, bind, getWireModelInfo, setFieldValue, onFormReset, toNumber } from '../utils'

export function slider() {
  return {
    input: null,
    value: null,

    init() {
      this.input = queryData(this.$root, 'control')
      this.$nextTick(() => this.updateRange())

      if (this.$wire) {
        const prop = getWireModelInfo(this.input)

        if (prop) {
          this.$wire.$watch(prop.name, () => this.updateRange())
        }
      }

      // A range ignores readonly: kept by hand.
      if (this.isReadonly()) this.input.setAttribute('aria-readonly', 'true')

      onFormReset(this.$root, this.input.form, () => this.updateRange())

      bind(this.input, {
        ['@input']: () => this.updateRange(),
        ['@keydown']: (e) => {
          if (this.isReadonly() && ['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End', 'PageUp', 'PageDown'].includes(e.key)) e.preventDefault()
        },
        ['@pointerdown']: (e) => {
          if (this.isReadonly()) e.preventDefault()
        },
      })

      bind(queryData(this.$root, 'slider-ticks'), {
        ['@click']: (e) => {
          const ticks = queryAllData(this.$root, 'slider-tick')
          const clickX = e.clientX

          let closestTick = null
          let minDistance = Infinity

          ticks.forEach((tick) => {
            const rect = tick.getBoundingClientRect()
            const centerX = rect.left + rect.width / 2
            const distance = Math.abs(clickX - centerX)

            if (distance < minDistance) {
              minDistance = distance
              closestTick = tick
            }
          })

          if (closestTick) {
            const value = toNumber(closestTick.getAttribute('data-value')) ?? toNumber(closestTick.textContent)

            if (value !== null) this.setValue(value)
          }
        }
      })
    },

    isReadonly() {
      return this.input.hasAttribute('readonly')
    },

    setValue(value) {
      if (this.input.disabled || this.isReadonly()) return

      setFieldValue(this.input, value)
    },

    updateRange() {
      const min = toNumber(this.input.min, 0)
      const max = toNumber(this.input.max, 100)
      const val = toNumber(this.input.value, min)
      const p = max === min ? 0 : ((val - min) * 100) / (max - min)

      this.value = this.input.value

      if (this.input.id) {
        queryAllData(document, 'slider-value', this.input.id)
          .forEach((el) => { el.textContent = this.input.value })
      }

      this.input.style.setProperty('--range-percent', `${p}%`)
      this.input.toggleAttribute('data-low', p < 50)
    }
  }
}

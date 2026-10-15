import { bind, clamp, onFormReset, onLivewireCommit, toNumber } from '../utils'

export function textarea({ maxRows = null, counter = null, length = 0 } = {}) {
  return {
    length,

    init() {
      const el = this.$el.querySelector('textarea')
      const minRows = toNumber(el.getAttribute('rows'))
      const autoRows = minRows && minRows > 0 && maxRows && maxRows > minRows

      const sync = () => {
        if (counter) {
          this.length = el.value.length
        }

        if (autoRows) {
          this.resizeRows(el, minRows, maxRows)
        }
      }

      sync()

      bind(el, {
        ['@input']: sync,
      })

      onFormReset(this.$root, el.form, sync)

      this._stopCommits = onLivewireCommit(({ component, succeed }) => {
        if (!component?.el?.contains(el)) return

        succeed(() => this.$nextTick(sync))
      })
    },

    destroy() {
      this._stopCommits?.()
    },

    resizeRows(el, minRows, maxRows) {
      el.rows = minRows

      const style = getComputedStyle(el)
      const padding = parseFloat(style.paddingTop) + parseFloat(style.paddingBottom)
      const lineHeight = toNumber(style.lineHeight) || toNumber(style.fontSize, 0) * 1.2 || 16
      const rows = Math.round((el.scrollHeight - padding) / lineHeight)

      el.rows = clamp(rows, minRows, maxRows)
    }
  }
}

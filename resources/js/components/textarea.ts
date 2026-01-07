import { bind } from '../utils'

export function textarea(maxRows: number) {
  return {
    init () {
      const minRows = parseInt(this.$el.getAttribute('rows'))

      if (minRows && minRows > 0 && maxRows && maxRows > minRows) {
        bind(this.$el, {
          ['@input']() {
            this.autoRows(minRows, maxRows)
          }
        })
      }
    },

    autoRows(minRows: number, maxRows: number) {
      this.$el.rows = minRows

      const style = getComputedStyle(this.$el)
      const lineHeight = parseFloat(style.lineHeight)
      const padding = parseFloat(style.paddingTop) + parseFloat(style.paddingBottom)
      const rows = Math.round((this.$el.scrollHeight - padding) / lineHeight)

      this.$el.rows = Math.min(Math.max(rows, minRows), maxRows)
    }
  }
}

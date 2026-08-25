import { bind } from '../utils'

export function textarea({ maxRows = null, counter = null, length = 0 } = {}) {
  return {
    length,

    init() {
      const el = this.$el.querySelector('textarea')
      const minRows = parseInt(el.getAttribute('rows'))
      const autoRows = minRows && minRows > 0 && maxRows && maxRows > minRows

      if (counter) {
        this.length = el.value.length
      }

      if (autoRows) {
        this.resizeRows(el, minRows, maxRows)
      }

      bind(el, {
        ['@input']: () => {
          if (counter) {
            this.length = el.value.length
          }

          if (autoRows) {
            this.resizeRows(el, minRows, maxRows)
          }
        },
      })
    },

    resizeRows(el: HTMLTextAreaElement, minRows: number, maxRows: number) {
      el.rows = minRows

      const style = getComputedStyle(el)
      const padding = parseFloat(style.paddingTop) + parseFloat(style.paddingBottom)
      const lineHeight = parseFloat(style.lineHeight) || parseFloat(style.fontSize) * 1.2 || 16
      const rows = Math.round((el.scrollHeight - padding) / lineHeight)

      el.rows = Math.min(Math.max(rows, minRows), maxRows)
    }
  }
}

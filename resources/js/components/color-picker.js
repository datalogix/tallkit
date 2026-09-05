import { normalizeColor } from '../utils'
import { bindableField } from '../mixins/bindable-field'

export function colorPicker({ value = null, format = 'hex' } = {}) {
  const _bindableField = bindableField({ key: 'color-picker' })

  return {
    ..._bindableField,

    value,
    format,

    init() {
      _bindableField.init.call(this)
    },

    hasEyeDropper() {
      return typeof window !== 'undefined' && 'EyeDropper' in window
    },

    pick(color) {
      if (this.field.disabled) return

      const normalized = color ? (normalizeColor(color, this.format) ?? color) : null

      this.value = normalized
    },

    commitTyped(raw) {
      if (this.field.disabled) return

      if (!raw) {
        this.pick(null)
        return
      }

      const normalized = normalizeColor(raw, this.format)

      if (normalized) {
        this.pick(normalized)
      } else {
        this.field.value = this.value ?? ''
      }
    },

    async dropColor() {
      if (!this.hasEyeDropper() || this.field.disabled) return

      try {
        const result = await new window.EyeDropper().open()
        this.pick(result.sRGBHex)
      } catch {
        //
      }
    },

    clear() {
      this.pick(null)
    },
  }
}

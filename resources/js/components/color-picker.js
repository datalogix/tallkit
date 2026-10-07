import { normalizeColor } from '../utils'
import { bindableField } from '../mixins/bindable-field'

export function colorPicker({ value = null, format = null } = {}) {
  const _bindableField = bindableField({ key: 'color-picker' })

  return {
    ..._bindableField,

    value,
    format: format ?? 'hex',

    init() {
      _bindableField.init.call(this)
    },

    hasEyeDropper() {
      return typeof window !== 'undefined' && 'EyeDropper' in window
    },

    isDisabled() {
      return !!this.field?.disabled
    },

    pick(color) {
      if (this.isDisabled()) return

      const normalized = color ? (normalizeColor(color, this.format) ?? color) : null

      if (normalized === this.value) return

      this.value = normalized
      this.dispatchPicked(normalized)
    },

    commitTyped(raw) {
      if (this.isDisabled()) return

      if (!raw) {
        this.pick(null)
        return
      }

      const normalized = normalizeColor(raw, this.format)

      if (normalized) {
        this.pick(normalized)
      } else {
        if (this.field) this.field.value = this.value ?? ''
      }
    },

    async dropColor() {
      if (!this.hasEyeDropper() || this.isDisabled()) return

      try {
        const result = await new window.EyeDropper().open()
        this.pick(result.sRGBHex)
      } catch {
      }
    },

    clear() {
      this.pick(null)
    },
  }
}

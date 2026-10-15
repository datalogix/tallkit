import { formatColor, normalizeColor, parseColor, setFieldValue } from '../utils'
import { bindableField } from '../mixins/bindable-field'

export function colorPicker({ value = null, format = null } = {}) {
  const _bindableField = bindableField({
    key: 'color-picker',
    deserialize(raw) {
      if (this.field && document.activeElement === this.field && raw === this.field.value) return this.value

      return raw ? (normalizeColor(raw, this.format) ?? raw) : null
    },
  })

  return {
    ..._bindableField,

    value,
    format: format ?? 'hex',
    draft: null,

    init() {
      if (this.value) this.value = normalizeColor(this.value, this.format) ?? this.value

      _bindableField.init.call(this)
    },

    shownValue() {
      return this.draft ?? this.value
    },

    hexValue() {
      return normalizeColor(this.shownValue(), 'hex') ?? '#000000'
    },

    fromNative(hex) {
      const next = parseColor(hex)
      const current = parseColor(this.value)

      if (!next) return null
      if (current) next.a = current.a

      return formatColor(next, this.format)
    },

    previewNative(hex) {
      if (this.isLocked()) return

      this.draft = this.fromNative(hex)
    },

    pickNative(hex) {
      this.draft = null
      this.pick(this.fromNative(hex))
    },

    hasEyeDropper() {
      return typeof window !== 'undefined' && 'EyeDropper' in window
    },

    isLocked() {
      return !!(this.field?.disabled || this.field?.readOnly)
    },

    setLive(color) {
      this.value = color ? (normalizeColor(color, this.format) ?? color) : null
    },

    swatchStyle(color) {
      if (!color) return { backgroundColor: 'transparent', backgroundImage: 'none', backgroundSize: 'auto' }

      return {
        backgroundColor: 'transparent',
        backgroundImage: `linear-gradient(${color}, ${color}), repeating-conic-gradient(#d4d4d8 0 25%, #fff 0 50%)`,
        backgroundSize: 'auto, 8px 8px',
      }
    },

    pick(color) {
      if (this.isLocked()) return

      const normalized = color ? (normalizeColor(color, this.format) ?? color) : null

      if (normalized === this.value) return

      this.value = normalized
      this.dispatchPicked(normalized)
    },

    commitTyped(raw) {
      if (this.isLocked()) return

      const normalized = raw ? normalizeColor(raw, this.format) : null

      if (!raw || normalized) this.pick(normalized)

      this.$nextTick(() => {
        const current = this.value ?? ''

        if (this.field && this.field.value !== current) setFieldValue(this.field, current)
      })
    },

    isActive(swatch) {
      if (!this.value) return false

      return (normalizeColor(swatch, this.format) ?? swatch) === (normalizeColor(this.value, this.format) ?? this.value)
    },

    async dropColor() {
      if (!this.hasEyeDropper() || this.isLocked()) return

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

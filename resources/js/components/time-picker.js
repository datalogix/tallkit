import { parseCommaList, padDatePart, toMinutes, parseTimeToken } from '../utils'
import { popover } from './popover'
import { bindableField } from '../mixins/bindable-field'

const FORMATS = ['12-hour', '24-hour']
const MINUTES_IN_DAY = 24 * 60

export function timePicker({
  value = null,
  multiple = null,
  format = null,
  locale = null,
  interval = null,
  min = null,
  max = null,
  unavailable = null,
  openTo = null,
  type = null,
} = {}) {
  if (format && !FORMATS.includes(format)) {
    console.warn(`[tallkit] tk:time-picker received an invalid "format" ("${format}"). Expected one of: ${FORMATS.join(', ')}. Falling back to the locale default.`)
    format = null
  }

  interval = Math.max(1, Number(interval) || 30)
  min = min ? parseTimeToken(min) : null
  max = max ? parseTimeToken(max) : null
  openTo = openTo ? parseTimeToken(openTo) : null
  multiple = Boolean(multiple)

  const unavailableRanges = parseCommaList(unavailable)
    .map((token) => {
      if (token.includes('-')) {
        const [start, end] = token.split('-').map((part) => parseTimeToken(part))

        return start && end ? [start, end] : null
      }

      const single = parseTimeToken(token)

      return single ? [single, single] : null
    })
    .filter(Boolean)

  const _popover = popover({ mode: 'dropdown', position: 'bottom', align: 'start', matchTriggerWidth: true })
  const _bindableField = bindableField({
    key: 'time-picker',
    serialize() { return multiple ? (this.value ?? []).join(',') : (this.value ?? null) },
    deserialize(raw) { return this.parseInitialValue(raw) },
  })

  return {
    ..._popover,
    ..._bindableField,

    value: null,
    typed: '',
    typing: false,

    locale: locale || (typeof navigator !== 'undefined' ? navigator.language : 'en-US'),

    init() {
      _popover.init.call(this)

      this.value = this.parseInitialValue(value)

      _bindableField.init.call(this)
      this.syncTyped()

      this.$watch('value', () => this.syncTyped())

      this.$watch('typed', () => {
        if (!this.typing) return

        this.commitTyped()
      })
    },

    onOpen() {
      _popover.onOpen.call(this)
      this.$nextTick(() => this.scrollToSelected())
    },

    parseInitialValue(raw) {
      if (multiple) {
        if (!raw) return []

        const list = Array.isArray(raw) ? raw : parseCommaList(raw)

        return list.map((v) => parseTimeToken(v)).filter(Boolean)
      }

      if (!raw) return null
      if (Array.isArray(raw)) raw = raw[0]

      return parseTimeToken(raw)
    },

    slots() {
      const values = []

      for (let m = 0; m < MINUTES_IN_DAY; m += interval) {
        values.push(`${padDatePart(Math.floor(m / 60))}:${padDatePart(m % 60)}`)
      }

      return values
    },

    isTimeDisabled(hhmm) {
      if (min && hhmm < min) return true
      if (max && hhmm > max) return true

      return unavailableRanges.some(([start, end]) => hhmm >= start && hhmm <= end)
    },

    isSelected(hhmm) {
      if (multiple) return (this.value ?? []).includes(hhmm)

      return this.value === hhmm
    },

    select(hhmm) {
      if (this.isTimeDisabled(hhmm)) return

      if (multiple) {
        this.toggleMultiple(hhmm)
        return
      }

      this.value = this.value === hhmm ? null : hhmm
      this.close()
    },

    toggleMultiple(hhmm) {
      const current = this.value ?? []

      this.value = current.includes(hhmm)
        ? current.filter((v) => v !== hhmm)
        : [...current, hhmm].sort()
    },

    formatter() {
      const hour12 = format === '12-hour' ? true : format === '24-hour' ? false : undefined
      const options = { hour: 'numeric', minute: '2-digit', hour12 }

      try {
        return new Intl.DateTimeFormat(this.locale, options)
      } catch {
        return new Intl.DateTimeFormat(undefined, options)
      }
    },

    formatSlot(hhmm) {
      const [h, m] = hhmm.split(':').map(Number)

      return this.formatter().format(new Date(2000, 0, 1, h, m))
    },

    formatted() {
      if (multiple) {
        return (this.value ?? []).length ? this.value.map((v) => this.formatSlot(v)).join(', ') : null
      }

      return this.value ? this.formatSlot(this.value) : null
    },

    typable() {
      return type === 'input' && !multiple
    },

    maskPattern() {
      return '99:99'
    },

    syncTyped() {
      if (!this.typable()) return

      this.typed = this.value ?? ''
    },

    commitTyped() {
      if (!this.typable()) return
      if ((this.typed.match(/\d/g) ?? []).length < 4) return

      const parsed = parseTimeToken(this.typed)

      if (parsed && !this.isTimeDisabled(parsed)) {
        this.value = parsed
      }
    },

    confirmTyped() {
      this.commitTyped()
      this.typing = false
      this.syncTyped()
      this.close()
    },

    onFieldBlur(event) {
      if (!this.$root.contains(event.relatedTarget)) {
        this.confirmTyped()
        return
      }

      this.commitTyped()
      this.typing = false
      this.syncTyped()
    },

    clear() {
      this.value = multiple ? [] : null
      this.typed = ''
    },

    nearestSlot(hhmm) {
      const target = toMinutes(hhmm)
      const values = this.slots()

      return values.reduce((closest, slot) => (
        Math.abs(toMinutes(slot) - target) < Math.abs(toMinutes(closest) - target) ? slot : closest
      ), values[0])
    },

    scrollToSelected() {
      const active = this.$root.querySelector('[data-active="true"]')
      const anchor = active ?? (openTo ? this.$root.querySelector(`[data-slot="${this.nearestSlot(openTo)}"]`) : null)

      anchor?.scrollIntoView({ block: 'nearest' })
    },
  }
}

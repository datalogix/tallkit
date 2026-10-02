import { queryData, parseCommaList, padDatePart, timeToMinutes, parseTypedTime, resolveLocale, toNumber } from '../utils'
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
  trigger = null,
} = {}) {
  if (format && !FORMATS.includes(format)) {
    console.warn(`[tallkit] tk:time-picker received an invalid "format" ("${format}"). Expected one of: ${FORMATS.join(', ')}. Falling back to the locale default.`)
    format = null
  }

  interval = Math.max(1, toNumber(interval) || 30)
  min = min ? parseTypedTime(min) : null
  max = max ? parseTypedTime(max) : null
  openTo = openTo ? parseTypedTime(openTo) : null
  multiple = Boolean(multiple)

  const unavailableRanges = parseCommaList(unavailable)
    .map((token) => {
      if (token.includes('-')) {
        const [start, end] = token.split('-').map((part) => parseTypedTime(part))

        return start && end ? [start, end] : null
      }

      const single = parseTypedTime(token)

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

    locale: resolveLocale(locale),

    init() {
      _popover.init.call(this)

      this.value = this.parseInitialValue(value)

      _bindableField.init.call(this)
      this.syncTyped()

      // Not while typed in: a time read halfway ("09:30" before "PM") would be written over it.
      this.$watch('value', () => {
        if (!this.typing) this.syncTyped()
      })

      this.$watch('typed', () => {
        if (!this.typing) return

        this.commitTyped()
      })
    },

    isDisabled() {
      return !!queryData(this.$root, 'control')?.disabled
    },

    open(focus = true) {
      if (this.isDisabled()) return

      _popover.open.call(this, focus)
    },

    onOpen() {
      _popover.onOpen.call(this)
      this.$nextTick(() => this.scrollToSelected())
    },

    parseInitialValue(raw) {
      if (multiple) {
        if (!raw) return []

        const list = Array.isArray(raw) ? raw : parseCommaList(raw)

        return list.map((v) => parseTypedTime(v)).filter(Boolean)
      }

      if (!raw) return null
      if (Array.isArray(raw)) raw = raw[0]

      return parseTypedTime(raw)
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
      if (this.isDisabled()) return
      if (this.isTimeDisabled(hhmm)) return

      if (multiple) {
        this.toggleMultiple(hhmm)
        this.dispatchPicked(this.value)
        return
      }

      this.value = this.value === hhmm ? null : hhmm
      this.dispatchPicked(this.value)
      this.close()
    },

    toggleMultiple(hhmm) {
      const current = this.value ?? []

      this.value = current.includes(hhmm)
        ? current.filter((v) => v !== hhmm)
        : [...current, hhmm].sort()
    },

    usesHour12() {
      if (format === '12-hour') return true
      if (format === '24-hour') return false

      try {
        return !!new Intl.DateTimeFormat(this.locale, { hour: 'numeric' }).resolvedOptions().hour12
      } catch {
        return false
      }
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
      return trigger === 'input' && !multiple
    },

    maskPattern() {
      return this.usesHour12() ? '99:99 aa' : '99:99'
    },

    editable(hhmm) {
      if (!hhmm || !this.usesHour12()) return hhmm ?? ''

      const [h, m] = hhmm.split(':').map(Number)

      return `${padDatePart(h % 12 || 12)}:${padDatePart(m)} ${h < 12 ? 'AM' : 'PM'}`
    },

    syncTyped() {
      if (!this.typable()) return

      this.typed = this.editable(this.value)
    },

    commitTyped() {
      if (this.isDisabled()) return
      if (!this.typable()) return
      if ((this.typed.match(/\d/g) ?? []).length < 4) return

      const parsed = parseTypedTime(this.typed)

      if (parsed && !this.isTimeDisabled(parsed) && parsed !== this.value) {
        this.value = parsed
        this.dispatchPicked(parsed)
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
      const target = timeToMinutes(hhmm)
      const values = this.slots()

      return values.reduce((closest, slot) => (
        Math.abs(timeToMinutes(slot) - target) < Math.abs(timeToMinutes(closest) - target) ? slot : closest
      ), values[0])
    },

    moveSlotFocus(event) {
      if (!['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) return

      const options = [...event.currentTarget.querySelectorAll('[role=option]')].filter((option) => !option.disabled)

      if (!options.length) return

      event.preventDefault()

      const index = options.indexOf(document.activeElement)
      const next = {
        ArrowDown: Math.min(index + 1, options.length - 1),
        ArrowUp: Math.max(index - 1, 0),
        Home: 0,
        End: options.length - 1,
      }[event.key]

      options[index === -1 ? 0 : next].focus()
    },

    scrollToSelected() {
      const active = this.$root.querySelector('[data-active="true"]')
      const anchor = active ?? (openTo ? this.$root.querySelector(`[data-slot="${this.nearestSlot(openTo)}"]`) : null)

      anchor?.scrollIntoView({ block: 'nearest' })
    },
  }
}

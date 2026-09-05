import { isoOf, parseIso, startOfMonth, startOfWeek, endOfMonth, addDays, addMonths, formatEditable, parseTypedDate, localeDateOrder } from '../utils'
import { popover } from './popover'
import { calendar } from './calendar'
import { bindableField } from '../mixins/bindable-field'

const DATE_STYLES = ['full', 'long', 'medium', 'short']
const DEFAULT_FORMAT = 'medium'

export function datePicker({
  mode = null,
  multiple = null,
  format = null,
  labels = null,
  type = null,
  openTo = null,
  forceOpenTo = null,
  withConfirmation = null,
  ...calendarOptions
} = {}) {
  if (format && !DATE_STYLES.includes(format)) {
    console.warn(`[tallkit] tk:date-picker received an invalid "format" ("${format}"). Expected one of: ${DATE_STYLES.join(', ')}. Falling back to "${DEFAULT_FORMAT}".`)
    format = DEFAULT_FORMAT
  }

  const _popover = popover({ mode: 'dropdown', position: 'bottom', align: 'start' })
  const _calendar = calendar({ mode, multiple, openTo, ...calendarOptions })
  const _bindableField = bindableField({
    key: 'date-picker',
    property: 'committed',
    serialize() { return this.committedString() },
    deserialize(raw) { return this.parseInitialValue(raw) },
  })

  return {
    ..._popover,
    ..._calendar,
    ..._bindableField,

    committed: null,
    typed: '',
    typing: false,

    init() {
      _popover.init.call(this)
      _calendar.init.call(this)

      this.committed = this.value

      _bindableField.init.call(this)

      if (JSON.stringify(this.value) !== JSON.stringify(this.committed)) {
        this.value = this.committed
      }

      this.syncTyped()

      this.$watch('value', () => {
        this.syncTyped()

        if (withConfirmation) return

        this.committed = this.value

        if (multiple) return
        if (mode === 'range' && !(this.value?.start && this.value?.end)) return
        if (this.typing) return

        this.close()
      })

      this.$watch('committed', () => {
        if (JSON.stringify(this.value) !== JSON.stringify(this.committed)) {
          this.value = this.committed
        }
      })

      this.$watch('typed', () => {
        if (!this.typing) return

        this.commitTyped()
      })
    },

    onOpen() {
      if (withConfirmation) this.value = this.committed
      if (forceOpenTo && openTo) this.anchorMonth = startOfMonth(parseIso(openTo))

      _popover.onOpen.call(this)
    },

    apply() {
      this.committed = this.value
      this.close()
    },

    cancel() {
      this.value = this.committed
      this.close()
    },

    setSingleValue(iso) {
      if (mode === 'range' || multiple) return
      if (iso && this.isDayDisabled(iso)) return

      this.value = iso || null
      this.focused = this.value ?? this.focused
      this.$dispatch('calendar-picked', { value: this.value })
    },

    formatted() {
      if (!this.value) return null

      let fmt

      try {
        fmt = new Intl.DateTimeFormat(this.locale, { dateStyle: format ?? DEFAULT_FORMAT })
      } catch {
        fmt = new Intl.DateTimeFormat(undefined, { dateStyle: DEFAULT_FORMAT })
      }

      if (mode === 'range') {
        if (!this.value.start || !this.value.end) return null

        const start = parseIso(this.value.start)
        const end = parseIso(this.value.end)

        return fmt.formatRange ? fmt.formatRange(start, end) : `${fmt.format(start)} – ${fmt.format(end)}`
      }

      if (multiple) {
        return this.value.length ? `${this.value.length} ${labels?.selected ?? 'selected'}` : null
      }

      return fmt.format(parseIso(this.value))
    },

    committedString() {
      if (mode === 'range') {
        if (!this.committed?.start) return null

        return this.committed.end ? `${this.committed.start}/${this.committed.end}` : this.committed.start
      }

      if (multiple) return (this.committed ?? []).join(',')

      return this.committed ?? null
    },

    typable() {
      return type === 'input' && !multiple
    },

    maskPattern() {
      const single = localeDateOrder(this.locale).map((part) => (part === 'year' ? '9999' : '99')).join('/')

      return mode === 'range' ? `${single} – ${single}` : single
    },

    requiredDigitCount() {
      return mode === 'range' ? 16 : 8
    },

    syncTyped() {
      if (!this.typable()) return

      this.typed = this.formattedEditable()
    },

    formattedEditable() {
      if (mode === 'range') {
        const start = this.value?.start ? formatEditable(this.value.start, this.locale) : ''
        const end = this.value?.end ? formatEditable(this.value.end, this.locale) : ''

        if (!start && !end) return ''

        return `${start} – ${end}`
      }

      return this.value ? formatEditable(this.value, this.locale) : ''
    },

    commitTyped() {
      if (!this.typable()) return
      if ((this.typed.match(/\d/g) ?? []).length < this.requiredDigitCount()) return

      if (mode === 'range') {
        const [rawStart, rawEnd] = this.typed.split(/\s*[–—]\s*/)
        const start = parseTypedDate(rawStart, this.locale)
        const end = parseTypedDate(rawEnd, this.locale)

        if (start) {
          this.setRangeBound('start', start)
          this.anchorMonth = startOfMonth(parseIso(start))
        }

        if (end) {
          this.setRangeBound('end', end)
          this.anchorMonth = startOfMonth(parseIso(end))
        }
      } else {
        const iso = parseTypedDate(this.typed, this.locale)

        if (iso && !this.isDayDisabled(iso)) {
          this.value = iso
          this.focused = iso
          this.anchorMonth = startOfMonth(parseIso(iso))
          this.$dispatch('calendar-picked', { value: iso })
        }
      }
    },

    confirmTyped() {
      this.commitTyped()
      this.typing = false
      this.syncTyped()

      if (!withConfirmation) this.close()
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

    presetRange(key) {
      if (mode !== 'range') return null

      const today = isoOf(new Date())
      const todayDate = parseIso(today)

      switch (key) {
        case 'today':
          return { start: today, end: today }
        case 'yesterday': {
          const yesterday = addDays(today, -1)

          return { start: yesterday, end: yesterday }
        }
        case 'thisWeek':
          return { start: startOfWeek(todayDate, this.startDay), end: today }
        case 'last7Days':
          return { start: addDays(today, -6), end: today }
        case 'last14Days':
          return { start: addDays(today, -13), end: today }
        case 'last30Days':
          return { start: addDays(today, -29), end: today }
        case 'thisMonth':
          return { start: isoOf(startOfMonth(todayDate)), end: isoOf(endOfMonth(todayDate)) }
        case 'lastMonth': {
          const lastMonth = addMonths(todayDate, -1)

          return { start: isoOf(startOfMonth(lastMonth)), end: isoOf(endOfMonth(lastMonth)) }
        }
        case 'thisYear':
          return { start: `${todayDate.getFullYear()}-01-01`, end: `${todayDate.getFullYear()}-12-31` }
        case 'lastYear':
          return { start: `${todayDate.getFullYear() - 1}-01-01`, end: `${todayDate.getFullYear() - 1}-12-31` }
        default:
          return null
      }
    },

    isPresetActive(key) {
      const range = this.presetRange(key)
      if (!range) return false

      return this.value?.start === range.start && this.value?.end === range.end
    },

    applyPreset(key) {
      const range = this.presetRange(key)
      if (!range) return

      this.value = range
      this.focused = range.end
      this.$dispatch('calendar-picked', { value: range })
    },
  }
}

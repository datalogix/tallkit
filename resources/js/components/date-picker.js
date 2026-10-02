import { queryData, formatIsoDate, parseIsoDate, startOfMonth, startOfWeek, endOfMonth, addDays, addMonths, formatTypedDate, parseTypedDate, localeDateOrder } from '../utils'

const startOfQuarter = (date) => new Date(date.getFullYear(), Math.floor(date.getMonth() / 3) * 3, 1)
const endOfQuarter = (date) => new Date(date.getFullYear(), Math.floor(date.getMonth() / 3) * 3 + 3, 0)
import { popover } from './popover'
import { calendar } from './calendar'
import { bindableField } from '../mixins/bindable-field'

const DATE_STYLES = ['full', 'long', 'medium', 'short']
const DEFAULT_FORMAT = 'medium'

export function datePicker({
  range = false,
  dateRange = false,
  multiple = null,
  format = null,
  trigger = null,
  openTo = null,
  forceOpenTo = null,
  confirm = null,
  ...calendarOptions
} = {}) {
  if (format && !DATE_STYLES.includes(format)) {
    console.warn(`[tallkit] tk:date-picker received an invalid "format" ("${format}"). Expected one of: ${DATE_STYLES.join(', ')}. Falling back to "${DEFAULT_FORMAT}".`)
    format = DEFAULT_FORMAT
  }

  const mode = range ? 'range' : null
  const _popover = popover({ mode: 'dropdown', position: 'bottom', align: 'start' })
  const _calendar = calendar({ range, multiple, openTo, ...calendarOptions })
  const _bindableField = bindableField({
    key: 'date-picker',
    property: 'committed',
    serialize() { return this.committedString() },
    deserialize(raw) {
      this.preset = raw?.preset ?? null

      return this.parseInitialValue(raw)
    },
    // A DateRange property gets an object, so the preset reaches the server.
    toWire: dateRange ? function () { return this.committedRange() } : null,
  })

  return {
    ..._popover,
    ..._calendar,
    ..._bindableField,

    committed: null,
    preset: null,
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

        if (confirm) return

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

    isDisabled() {
      return !!queryData(this.$root, 'control')?.disabled
    },

    open(focus = true) {
      if (this.isDisabled()) return

      _popover.open.call(this, focus)
    },

    onOpen() {
      if (confirm) this.value = this.committed
      if (forceOpenTo && openTo) this.anchorMonth = startOfMonth(parseIsoDate(openTo))

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
      if (this.isDisabled()) return
      if (mode === 'range' || multiple) return
      if (iso && this.isDayDisabled(iso)) return

      this.value = iso || null
      this.focused = this.value ?? this.focused
      this.dispatchPicked(this.value)
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

        const start = parseIsoDate(this.value.start)
        const end = parseIsoDate(this.value.end)

        // format(null) is the epoch.
        if (!start || !end) return null

        return fmt.formatRange ? fmt.formatRange(start, end) : `${fmt.format(start)} – ${fmt.format(end)}`
      }

      if (multiple) {
        const dates = (this.value ?? []).map(parseIsoDate).filter(Boolean)

        return dates.length ? dates.map((date) => fmt.format(date)).join(', ') : null
      }

      const date = parseIsoDate(this.value)

      return date ? fmt.format(date) : null
    },

    committedRange() {
      if (!this.committed?.start) return null

      const range = { start: this.committed.start, end: this.committed.end ?? null }
      const preset = this.presetRange(this.preset)

      if (preset && preset.start === range.start && preset.end === range.end) range.preset = this.preset

      return range
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
      return trigger === 'input' && !multiple
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
        const start = this.value?.start ? formatTypedDate(this.value.start, this.locale) : ''
        const end = this.value?.end ? formatTypedDate(this.value.end, this.locale) : ''

        if (!start && !end) return ''

        return `${start} – ${end}`
      }

      return this.value ? formatTypedDate(this.value, this.locale) : ''
    },

    commitTyped() {
      if (this.isDisabled()) return
      if (!this.typable()) return
      if ((this.typed.match(/\d/g) ?? []).length < this.requiredDigitCount()) return

      if (mode === 'range') {
        const [rawStart, rawEnd] = this.typed.split(/\s*[–—]\s*/)
        const start = parseTypedDate(rawStart, this.locale)
        const end = parseTypedDate(rawEnd, this.locale)

        if (start) {
          this.setRangeBound('start', start)
          this.anchorMonth = startOfMonth(parseIsoDate(start))
        }

        if (end) {
          this.setRangeBound('end', end)
          this.anchorMonth = startOfMonth(parseIsoDate(end))
        }
      } else {
        const iso = parseTypedDate(this.typed, this.locale)

        if (iso && !this.isDayDisabled(iso)) {
          this.value = iso
          this.focused = iso
          this.anchorMonth = startOfMonth(parseIsoDate(iso))
          this.dispatchPicked(iso)
        }
      }
    },

    confirmTyped() {
      this.commitTyped()
      this.typing = false
      this.syncTyped()

      if (!confirm) this.close()
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

    // The same as the server's (TALLKit\Livewire\DateRangePreset::dates()).
    presetRange(key) {
      if (mode !== 'range') return null

      const today = formatIsoDate(new Date())
      const todayDate = parseIsoDate(today)
      const week = (iso) => {
        const start = startOfWeek(parseIsoDate(iso), this.startDay)

        return { start, end: addDays(start, 6) }
      }
      const month = (offset) => {
        const date = addMonths(todayDate, offset)

        return { start: formatIsoDate(startOfMonth(date)), end: formatIsoDate(endOfMonth(date)) }
      }
      const quarter = (offset) => {
        const date = addMonths(startOfQuarter(todayDate), offset * 3)

        return { start: formatIsoDate(startOfQuarter(date)), end: formatIsoDate(endOfQuarter(date)) }
      }
      const year = (offset) => ({ start: `${todayDate.getFullYear() + offset}-01-01`, end: `${todayDate.getFullYear() + offset}-12-31` })

      switch (key) {
        case 'today': return { start: today, end: today }
        case 'yesterday': return { start: addDays(today, -1), end: addDays(today, -1) }
        case 'tomorrow': return { start: addDays(today, 1), end: addDays(today, 1) }
        case 'thisWeek': return week(today)
        case 'lastWeek': return week(addDays(today, -7))
        case 'nextWeek': return week(addDays(today, 7))
        case 'last7Days': return { start: addDays(today, -6), end: today }
        case 'last14Days': return { start: addDays(today, -13), end: today }
        case 'last30Days': return { start: addDays(today, -29), end: today }
        case 'next7Days': return { start: today, end: addDays(today, 6) }
        case 'next14Days': return { start: today, end: addDays(today, 13) }
        case 'next30Days': return { start: today, end: addDays(today, 29) }
        case 'thisMonth': return month(0)
        case 'lastMonth': return month(-1)
        case 'nextMonth': return month(1)
        case 'thisQuarter': return quarter(0)
        case 'lastQuarter': return quarter(-1)
        case 'nextQuarter': return quarter(1)
        case 'thisYear': return year(0)
        case 'lastYear': return year(-1)
        case 'nextYear': return year(1)
        case 'yearToDate': return { start: `${todayDate.getFullYear()}-01-01`, end: today }
        case 'last3Months': return { start: this.shiftMonth(addDays(today, 1), -3), end: today }
        case 'last6Months': return { start: this.shiftMonth(addDays(today, 1), -6), end: today }
        case 'next3Months': return { start: today, end: this.shiftMonth(addDays(today, -1), 3) }
        case 'next6Months': return { start: today, end: this.shiftMonth(addDays(today, -1), 6) }
        default: return null
      }
    },

    presetAvailable(key) {
      const range = this.presetRange(key)

      return !!range && this.rangeAllowed(range.start, range.end)
    },

    isPresetActive(key) {
      const range = this.presetRange(key)
      if (!range) return false

      // Two presets can have the same days (this week and last 7 days).
      const chosen = this.presetRange(this.preset)
      if (chosen && this.value?.start === chosen.start && this.value?.end === chosen.end) return key === this.preset

      return this.value?.start === range.start && this.value?.end === range.end
    },

    applyPreset(key) {
      if (this.isDisabled()) return

      const range = this.presetRange(key)
      if (!range || !this.rangeAllowed(range.start, range.end)) return

      this.preset = key
      this.value = range
      this.focused = range.end
      this.dispatchPicked(range)
    },
  }
}

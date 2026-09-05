import {
  isRtl,
  isoOf,
  parseIso,
  startOfMonth,
  addMonths,
  addDays,
  sameMonth,
  diffDays,
  isoWeekNumber,
  resolveLocaleFirstDay,
  parseCommaList,
} from '../utils'
import { bindableField } from '../mixins/bindable-field'

export function calendar({
  value = null,
  multiple = false,
  mode = null,
  months = 1,
  min = null,
  max = null,
  unavailable = null,
  minRange = null,
  maxRange = null,
  static: isStatic = false,
  navigation = true,
  withToday = false,
  selectableHeader = false,
  fixedWeeks = false,
  startDay = null,
  openTo = null,
  weekNumbers = false,
  locale = null,
} = {}) {
  months = Math.max(1, Number(months) || 1)
  minRange = Number(minRange) || null
  maxRange = Number(maxRange) || null

  const _bindableField = bindableField({
    key: 'calendar-field',
    serialize() { return this.valueString() },
    deserialize(raw) { return this.parseInitialValue(raw) },
  })

  return {
    ..._bindableField,

    static: isStatic,
    navigation,
    withToday,
    selectableHeader,
    fixedWeeks,
    weekNumbers,
    locale: locale || (typeof navigator !== 'undefined' ? navigator.language : 'en-US'),
    startDay: 0,
    unavailable: parseCommaList(unavailable),

    value: null,
    anchorMonth: null,
    focused: null,
    hoverIso: null,
    rangeAnchor: null,

    init() {
      this.startDay = startDay !== null && startDay !== undefined ? Number(startDay) : resolveLocaleFirstDay(this.locale)
      this.value = this.parseInitialValue(value)

      _bindableField.init.call(this)

      this.anchorMonth = startOfMonth(this.firstAnchorDate())
      this.focused = this.firstSelectedIso() ?? isoOf(new Date())
    },

    parseInitialValue(raw) {
      if (mode === 'range') return this.normalizeRange(raw)
      if (multiple) return this.normalizeMultiple(raw)

      return this.normalizeSingle(raw)
    },

    normalizeSingle(raw) {
      if (!raw || typeof raw === 'object') return Array.isArray(raw) ? (raw[0] ?? null) : null

      return String(raw).trim() || null
    },

    normalizeMultiple(raw) {
      if (!raw) return []
      if (Array.isArray(raw)) return raw.filter(Boolean)

      return parseCommaList(raw)
    },

    normalizeRange(raw) {
      if (!raw) return null

      if (Array.isArray(raw)) {
        return raw[0] || raw[1] ? { start: raw[0] ?? null, end: raw[1] ?? null } : null
      }

      if (typeof raw === 'object') {
        return raw.start || raw.end ? { start: raw.start ?? null, end: raw.end ?? null } : null
      }

      const [start, end] = String(raw).split('/')

      return start?.trim() ? { start: start.trim(), end: end?.trim() || null } : null
    },

    valueString() {
      if (mode === 'range') {
        if (!this.value?.start) return null

        return this.value.end ? `${this.value.start}/${this.value.end}` : this.value.start
      }

      if (multiple) {
        return (this.value ?? []).join(',')
      }

      return this.value ?? null
    },

    firstAnchorDate() {
      const iso = this.firstSelectedIso()
      if (iso) return parseIso(iso)
      if (openTo) return parseIso(openTo) ?? new Date()

      return new Date()
    },

    firstSelectedIso() {
      if (mode === 'range') return this.value?.start ?? null
      if (multiple) return this.value?.[0] ?? null

      return this.value ?? null
    },

    monthAt(offset) {
      return addMonths(this.anchorMonth, offset)
    },

    isMonthVisible(date) {
      for (let i = 0; i < months; i++) {
        if (sameMonth(this.monthAt(i), date)) return true
      }

      return false
    },

    weekdayLabels() {
      const fmt = new Intl.DateTimeFormat(this.locale, { weekday: 'short' })
      const base = new Date(1970, 0, 4) // a Sunday

      return Array.from({ length: 7 }, (_, i) => {
        const date = new Date(base)
        date.setDate(base.getDate() + ((this.startDay + i) % 7))

        return fmt.format(date)
      })
    },

    monthLabel(monthIndex) {
      return new Intl.DateTimeFormat(this.locale, { month: 'long', year: 'numeric' }).format(this.monthAt(monthIndex))
    },

    monthOptions() {
      const fmt = new Intl.DateTimeFormat(this.locale, { month: 'long' })

      return Array.from({ length: 12 }, (_, i) => ({ value: i, label: fmt.format(new Date(2000, i, 1)) }))
    },

    yearOptions() {
      const span = 10
      const current = this.anchorMonth.getFullYear()

      return Array.from({ length: span * 2 + 1 }, (_, i) => current - span + i)
    },

    weeksFor(monthIndex) {
      const month = this.monthAt(monthIndex)
      const year = month.getFullYear()
      const monthNum = month.getMonth()
      const firstOfMonth = new Date(year, monthNum, 1)
      const daysInMonth = new Date(year, monthNum + 1, 0).getDate()
      const startOffset = (firstOfMonth.getDay() - this.startDay + 7) % 7

      let totalCells = Math.ceil((startOffset + daysInMonth) / 7) * 7
      if (this.fixedWeeks) totalCells = Math.max(totalCells, 42)

      const days = Array.from({ length: totalCells }, (_, i) => {
        const date = new Date(year, monthNum, i - startOffset + 1)

        return { iso: isoOf(date), label: date.getDate(), inMonth: date.getMonth() === monthNum }
      })

      const weeks = []

      for (let i = 0; i < days.length; i += 7) {
        const weekDays = days.slice(i, i + 7)

        const thursday = weekDays[(4 - this.startDay + 7) % 7]

        weeks.push({
          key: weekDays[0].iso,
          weekNumber: this.weekNumbers ? isoWeekNumber(parseIso(thursday.iso)) : null,
          days: weekDays,
        })
      }

      return weeks
    },

    currentMonthIndex() {
      return this.anchorMonth.getMonth()
    },

    setCurrentMonthIndex(index) {
      if (this.static || !this.navigation) return
      this.anchorMonth = new Date(this.anchorMonth.getFullYear(), Number(index), 1)
    },

    currentYear() {
      return this.anchorMonth.getFullYear()
    },

    setCurrentYear(year) {
      if (this.static || !this.navigation) return
      this.anchorMonth = new Date(Number(year), this.anchorMonth.getMonth(), 1)
    },

    prevMonth() {
      if (this.static || !this.navigation) return
      this.anchorMonth = addMonths(this.anchorMonth, -1)
    },

    nextMonth() {
      if (this.static || !this.navigation) return
      this.anchorMonth = addMonths(this.anchorMonth, 1)
    },

    goToToday() {
      if (this.static) return

      const today = new Date()

      if (!this.isMonthVisible(today)) {
        if (!this.navigation) return
        this.anchorMonth = startOfMonth(today)
        return
      }

      this.selectDate(isoOf(today))
    },

    isDayDisabled(iso) {
      if (this.static) return true
      if (min && iso < min) return true
      if (max && iso > max) return true
      if (this.unavailable.includes(iso)) return true
      if (this.isOutOfRangeSpan(iso)) return true

      return false
    },

    isOutOfRangeSpan(iso) {
      if (mode !== 'range') return false
      if (!minRange && !maxRange) return false
      if (!this.rangeAnchor || iso === this.rangeAnchor) return false

      const start = this.rangeAnchor <= iso ? this.rangeAnchor : iso
      const end = this.rangeAnchor <= iso ? iso : this.rangeAnchor
      const days = diffDays(start, end) + 1

      if (minRange && days < minRange) return true
      if (maxRange && days > maxRange) return true

      return false
    },

    isUnavailable(iso) {
      return !this.static && this.isDayDisabled(iso)
    },

    isSelected(iso) {
      if (mode === 'range') return this.value?.start === iso || this.value?.end === iso
      if (multiple) return (this.value ?? []).includes(iso)

      return this.value === iso
    },

    isToday(iso) {
      return iso === isoOf(new Date())
    },

    displayRange() {
      if (mode !== 'range') return null

      const start = this.value?.start ?? null
      const end = this.value?.end ?? (this.rangeAnchor ? this.hoverIso : null)

      if (!start) return null
      if (!end) return { lo: start, hi: start }

      return start <= end ? { lo: start, hi: end } : { lo: end, hi: start }
    },

    isRangeStart(iso) {
      const range = this.displayRange()

      return !!range && range.lo === iso
    },

    isRangeEnd(iso) {
      const range = this.displayRange()

      return !!range && range.hi === iso
    },

    isInRange(iso) {
      const range = this.displayRange()

      return !!range && iso > range.lo && iso < range.hi
    },

    selectDate(iso) {
      if (this.static || this.isDayDisabled(iso)) return

      if (mode === 'range') {
        this.pickRangeDate(iso)
        return
      }

      if (multiple) {
        this.toggleMultiple(iso)
        return
      }

      this.value = this.value === iso ? null : iso
      this.focused = iso
      this.$dispatch('calendar-picked', { value: this.value })
    },

    toggleMultiple(iso) {
      const current = this.value ?? []

      this.value = current.includes(iso)
        ? current.filter((d) => d !== iso)
        : [...current, iso].sort()

      this.focused = iso
      this.$dispatch('calendar-picked', { value: this.value })
    },

    pickRangeDate(iso) {
      if (!this.rangeAnchor) {
        this.rangeAnchor = iso
        this.value = { start: iso, end: null }
        this.focused = iso
        return
      }

      let [start, end] = this.rangeAnchor <= iso ? [this.rangeAnchor, iso] : [iso, this.rangeAnchor]
      const days = diffDays(start, end) + 1

      if ((minRange && days < minRange) || (maxRange && days > maxRange) || this.rangeContainsUnavailable(start, end)) {
        this.rangeAnchor = iso
        this.value = { start: iso, end: null }
        this.focused = iso
        return
      }

      this.value = { start, end }
      this.rangeAnchor = null
      this.hoverIso = null
      this.focused = iso
      this.$dispatch('calendar-picked', { value: this.value })
    },

    setRangeBound(part, iso) {
      if (mode !== 'range') return

      let next = { ...(this.value ?? { start: null, end: null }), [part]: iso || null }

      if (next.start && next.end && next.start > next.end) {
        next = { start: next.end, end: next.start }
      }

      if (next.start === (this.value?.start ?? null) && next.end === (this.value?.end ?? null)) return

      if (next.start && this.isDayDisabled(next.start)) return
      if (next.end && this.isDayDisabled(next.end)) return

      if (next.start && next.end) {
        const days = diffDays(next.start, next.end) + 1

        if ((minRange && days < minRange) || (maxRange && days > maxRange) || this.rangeContainsUnavailable(next.start, next.end)) {
          return
        }
      }

      this.value = next
      this.rangeAnchor = null
      this.hoverIso = null
      this.focused = iso || this.focused
      this.$dispatch('calendar-picked', { value: this.value })
    },

    rangeContainsUnavailable(start, end) {
      if (!this.unavailable.length) return false

      for (let cursor = start; cursor <= end; cursor = addDays(cursor, 1)) {
        if (this.unavailable.includes(cursor)) return true
      }

      return false
    },

    previewRange(iso) {
      if (mode === 'range' && this.rangeAnchor) this.hoverIso = iso
    },

    clear() {
      this.value = mode === 'range' ? null : (multiple ? [] : null)
      this.rangeAnchor = null
      this.hoverIso = null
    },

    onCellKeydown(event, iso) {
      const rtl = isRtl(this.$root)
      const deltas = { ArrowLeft: rtl ? 1 : -1, ArrowRight: rtl ? -1 : 1, ArrowUp: -7, ArrowDown: 7 }

      if (event.key in deltas) {
        event.preventDefault()
        this.focusIso(addDays(iso, deltas[event.key]))
      } else if (event.key === 'Home') {
        event.preventDefault()
        this.focusIso(this.weekEdge(iso, 'start'))
      } else if (event.key === 'End') {
        event.preventDefault()
        this.focusIso(this.weekEdge(iso, 'end'))
      } else if (event.key === 'PageUp') {
        event.preventDefault()
        this.focusIso(this.shiftMonth(iso, event.shiftKey ? -12 : -1))
      } else if (event.key === 'PageDown') {
        event.preventDefault()
        this.focusIso(this.shiftMonth(iso, event.shiftKey ? 12 : 1))
      } else if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault()
        this.selectDate(iso)
      }
    },

    weekEdge(iso, edge) {
      const jsDay = parseIso(iso).getDay()
      const offset = (jsDay - this.startDay + 7) % 7

      return edge === 'start' ? addDays(iso, -offset) : addDays(iso, 6 - offset)
    },

    shiftMonth(iso, deltaMonths) {
      const date = parseIso(iso)

      return isoOf(new Date(date.getFullYear(), date.getMonth() + deltaMonths, date.getDate()))
    },

    focusIso(iso) {
      const targetMonth = startOfMonth(parseIso(iso))

      if (!this.isMonthVisible(targetMonth)) {
        if (!this.navigation) return

        this.anchorMonth = targetMonth > this.anchorMonth ? addMonths(targetMonth, -(months - 1)) : targetMonth
      }

      this.focused = iso

      this.$nextTick(() => {
        this.$root.querySelector(`[data-iso="${iso}"]`)?.focus()
      })
    },
  }
}

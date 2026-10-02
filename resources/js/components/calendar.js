import { isRtl, formatIsoDate, parseIsoDate, startOfMonth, addMonths, addDays, isSameMonth, diffDays, isoWeekNumber, localeFirstDay, parseCommaList, normalizeIsoDate, emit, resolveLocale, toNumber } from '../utils'
import { bindableField } from '../mixins/bindable-field'

export function calendar({
  value = null,
  multiple = false,
  range = false,
  months = 1,
  min = null,
  max = null,
  unavailable = null,
  minRange = null,
  maxRange = null,
  static: isStatic = false,
  navigation = true,
  today = false,
  selectableHeader = false,
  fixedWeeks = false,
  startDay = null,
  openTo = null,
  weekNumbers = false,
  locale = null,
} = {}) {
  const mode = range ? 'range' : null
  months = Math.max(1, toNumber(months, 1))
  min = normalizeIsoDate(min)
  max = normalizeIsoDate(max)
  minRange = toNumber(minRange)
  maxRange = toNumber(maxRange)

  const _bindableField = bindableField({
    key: 'calendar-field',
    serialize() { return this.valueString() },
    deserialize(raw) { return this.parseInitialValue(raw) },
  })

  return {
    ..._bindableField,

    static: isStatic,
    navigation,
    today,
    selectableHeader,
    fixedWeeks,
    weekNumbers,
    locale: resolveLocale(locale),
    startDay: 0,
    unavailable: parseCommaList(unavailable).map(normalizeIsoDate).filter(Boolean),

    value: null,

    anchorMonth: null,

    focused: null,

    hoverIso: null,
    rangeAnchor: null,

    dispatchPicked(value) {
      emit(this.$root, 'picked', { value })
    },

    init() {
      this.startDay = toNumber(startDay) ?? localeFirstDay(this.locale)
      this.value = this.parseInitialValue(value)

      _bindableField.init.call(this)

      this.anchorMonth = startOfMonth(this.firstAnchorDate())
      this.focused = this.firstSelectedIso() ?? formatIsoDate(new Date())
    },

    parseInitialValue(raw) {
      if (mode === 'range') return this.normalizeRange(raw)
      if (multiple) return this.normalizeMultiple(raw)

      return this.normalizeSingle(raw)
    },

    normalizeSingle(raw) {
      if (!raw || typeof raw === 'object') return Array.isArray(raw) ? normalizeIsoDate(raw[0]) : null

      return normalizeIsoDate(raw)
    },

    normalizeMultiple(raw) {
      if (!raw) return []

      return (Array.isArray(raw) ? raw : parseCommaList(raw)).map(normalizeIsoDate).filter(Boolean)
    },

    normalizeRange(raw) {
      if (!raw) return null

      const range = (start, end) => {
        start = normalizeIsoDate(start)
        end = normalizeIsoDate(end)

        return start || end ? { start, end } : null
      }

      if (Array.isArray(raw)) return range(raw[0], raw[1])
      if (typeof raw === 'object') return range(raw.start, raw.end)

      const [start, end] = String(raw).split('/')

      return normalizeIsoDate(start) ? range(start, end) : null
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
      if (iso && parseIsoDate(iso)) return parseIsoDate(iso)
      if (openTo) return parseIsoDate(openTo) ?? new Date()

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
        if (isSameMonth(this.monthAt(i), date)) return true
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

    dayAriaLabel(iso) {
      return new Intl.DateTimeFormat(this.locale, { dateStyle: 'full' }).format(parseIsoDate(iso))
    },

    monthOptions() {
      const fmt = new Intl.DateTimeFormat(this.locale, { month: 'long' })

      return Array.from({ length: 12 }, (_, i) => ({ value: i, label: fmt.format(new Date(2000, i, 1)) }))
    },

    yearOptions() {
      const current = this.anchorMonth.getFullYear()
      const from = Math.min(min ? Number(min.slice(0, 4)) : current - 100, current)
      const to = Math.max(max ? Number(max.slice(0, 4)) : current + 100, current)

      return Array.from({ length: to - from + 1 }, (_, i) => from + i)
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

        return { iso: formatIsoDate(date), label: date.getDate(), inMonth: date.getMonth() === monthNum }
      })

      const weeks = []

      for (let i = 0; i < days.length; i += 7) {
        const weekDays = days.slice(i, i + 7)

        const thursday = weekDays[(4 - this.startDay + 7) % 7]

        weeks.push({
          key: weekDays[0].iso,
          weekNumber: this.weekNumbers ? isoWeekNumber(parseIsoDate(thursday.iso)) : null,
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

      this.selectDate(formatIsoDate(today))
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
      return iso === formatIsoDate(new Date())
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
      this.dispatchPicked(this.value)
    },

    toggleMultiple(iso) {
      const current = this.value ?? []

      this.value = current.includes(iso)
        ? current.filter((d) => d !== iso)
        : [...current, iso].sort()

      this.focused = iso
      this.dispatchPicked(this.value)
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
      this.dispatchPicked(this.value)
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
      this.dispatchPicked(this.value)
    },

    rangeAllowed(start, end) {
      if (this.static || !start || !end) return false
      if ((min && start < min) || (max && end > max)) return false

      const days = diffDays(start, end) + 1

      if ((minRange && days < minRange) || (maxRange && days > maxRange)) return false

      return !this.rangeContainsUnavailable(start, end)
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

    tabbableIso() {
      const usable = (iso) => !!iso && this.isMonthVisible(parseIsoDate(iso)) && !this.isDayDisabled(iso)

      for (const iso of [this.focused, this.firstSelectedIso(), formatIsoDate(new Date())]) {
        if (usable(iso)) return iso
      }

      for (let i = 0; i < months; i++) {
        const month = this.monthAt(i)
        const days = new Date(month.getFullYear(), month.getMonth() + 1, 0).getDate()

        for (let day = 1; day <= days; day++) {
          const iso = formatIsoDate(new Date(month.getFullYear(), month.getMonth(), day))

          if (!this.isDayDisabled(iso)) return iso
        }
      }

      return null
    },

    enabledFrom(iso, step) {
      for (let i = 0; i < 366; i++, iso = addDays(iso, step)) {
        if (!this.isDayDisabled(iso)) return iso
      }

      return null
    },

    onCellKeydown(event, iso) {
      const rtl = isRtl(this.$root)
      const deltas = { ArrowLeft: rtl ? 1 : -1, ArrowRight: rtl ? -1 : 1, ArrowUp: -7, ArrowDown: 7 }
      let target = null
      let step = 1

      if (event.key in deltas) {
        target = addDays(iso, deltas[event.key])
        step = deltas[event.key]
      } else if (event.key === 'Home') {
        target = this.weekEdge(iso, 'start')
      } else if (event.key === 'End') {
        target = this.weekEdge(iso, 'end')
        step = -1
      } else if (event.key === 'PageUp') {
        target = this.shiftMonth(iso, event.shiftKey ? -12 : -1)
      } else if (event.key === 'PageDown') {
        target = this.shiftMonth(iso, event.shiftKey ? 12 : 1)
        step = -1
      }

      if (target) {
        event.preventDefault()

        const next = this.enabledFrom(target, step)
        if (next) this.focusIso(next)
      } else if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault()
        this.selectDate(iso)
      }
    },

    weekEdge(iso, edge) {
      const jsDay = parseIsoDate(iso).getDay()
      const offset = (jsDay - this.startDay + 7) % 7

      return edge === 'start' ? addDays(iso, -offset) : addDays(iso, 6 - offset)
    },

    shiftMonth(iso, deltaMonths) {
      const date = parseIsoDate(iso)
      const target = new Date(date.getFullYear(), date.getMonth() + deltaMonths, 1)
      const lastDay = new Date(target.getFullYear(), target.getMonth() + 1, 0).getDate()

      target.setDate(Math.min(date.getDate(), lastDay))

      return formatIsoDate(target)
    },

    focusIso(iso) {
      // Taken now: a change of month removes the cell the key came from.
      const root = this.$root
      const targetMonth = startOfMonth(parseIsoDate(iso))

      if (!this.isMonthVisible(targetMonth)) {
        if (!this.navigation) return

        this.anchorMonth = targetMonth > this.anchorMonth ? addMonths(targetMonth, -(months - 1)) : targetMonth
      }

      this.focused = iso

      this.$nextTick(() => {
        root.querySelector(`[data-iso="${iso}"]:not([data-outside-month])`)?.focus()
      })
    },
  }
}

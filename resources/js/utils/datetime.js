export function padDatePart(n) {
  return String(n).padStart(2, '0')
}

// By its local day: toISOString gives the UTC one.
export function formatIsoDate(date) {
  return `${date.getFullYear()}-${padDatePart(date.getMonth() + 1)}-${padDatePart(date.getDate())}`
}

export function normalizeIsoDate(value) {
  const match = /^(\d{4})-(\d{2})-(\d{2})(?:$|[T\s])/.exec(String(value ?? '').trim())

  if (!match) return null

  const [, y, m, d] = match.map(Number)
  const date = new Date(y, m - 1, d)

  if (date.getFullYear() !== y || date.getMonth() !== m - 1 || date.getDate() !== d) return null

  return `${match[1]}-${match[2]}-${match[3]}`
}

// Local midnight: new Date("YYYY-MM-DD") is UTC midnight.
export function parseIsoDate(iso) {
  iso = normalizeIsoDate(iso)

  if (!iso) return null

  const [y, m, d] = iso.split('-').map(Number)
  const date = new Date(y, m - 1, d)

  // Date would roll a day that doesn't exist into another month.
  if (date.getFullYear() !== y || date.getMonth() !== m - 1 || date.getDate() !== d) return null

  return date
}

export function startOfMonth(date) {
  return new Date(date.getFullYear(), date.getMonth(), 1)
}

export function addMonths(date, n) {
  return new Date(date.getFullYear(), date.getMonth() + n, 1)
}

export function endOfMonth(date) {
  return new Date(date.getFullYear(), date.getMonth() + 1, 0)
}

export function startOfWeek(date, startDay = 0) {
  const offset = (date.getDay() - startDay + 7) % 7

  return addDays(formatIsoDate(date), -offset)
}

export function addDays(iso, n) {
  const date = parseIsoDate(iso)
  date.setDate(date.getDate() + n)

  return formatIsoDate(date)
}

export function isSameMonth(a, b) {
  return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth()
}

// Rounded: a day across a daylight saving change is 23 or 25 hours.
export function diffDays(isoA, isoB) {
  return Math.round((parseIsoDate(isoB) - parseIsoDate(isoA)) / 86400000)
}

export function isoWeekNumber(date) {
  const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()))
  const dayNum = d.getUTCDay() || 7
  d.setUTCDate(d.getUTCDate() + 4 - dayNum)

  const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1))

  return Math.ceil((((d - yearStart) / 86400000) + 1) / 7)
}

export function localeFirstDay(locale) {
  try {
    const info = new Intl.Locale(locale).weekInfo ?? new Intl.Locale(locale).getWeekInfo?.()

    if (info?.firstDay) {
      return info.firstDay % 7
    }
  } catch {
  }

  return 0
}

export function localeDateOrder(locale) {
  try {
    const parts = new Intl.DateTimeFormat(locale, { year: 'numeric', month: '2-digit', day: '2-digit' })
      .formatToParts(new Date(2000, 0, 2))

    const order = parts.filter((part) => ['day', 'month', 'year'].includes(part.type)).map((part) => part.type)

    if (order.length === 3) return order
  } catch {
  }

  return ['month', 'day', 'year']
}

export function formatTypedDate(iso, locale) {
  const date = parseIsoDate(iso)
  if (!date) return ''

  return new Intl.DateTimeFormat(locale, { year: 'numeric', month: '2-digit', day: '2-digit' }).format(date)
}

export function parseTypedDate(text, locale) {
  if (!text) return null

  const digits = String(text).match(/\d+/g)
  if (!digits || digits.length < 3) return null

  const order = /^\s*\d{4}\D/.test(String(text)) ? ['year', 'month', 'day'] : localeDateOrder(locale)
  const values = {}

  order.forEach((type, index) => {
    values[type] = digits[index]
  })

  if (!values.day || !values.month || !values.year) return null

  const day = Number(values.day)
  const month = Number(values.month)
  let year = Number(values.year)

  if (values.year.length === 2) {
    year += year < 70 ? 2000 : 1900
  }

  const date = new Date(year, month - 1, day)

  // Date would roll a day that doesn't exist into the next month.
  if (date.getFullYear() !== year || date.getMonth() !== month - 1 || date.getDate() !== day) {
    return null
  }

  return formatIsoDate(date)
}

export function timeToMinutes(hhmm) {
  const [h, m] = hhmm.split(':').map(Number)

  return h * 60 + m
}

export function parseTypedTime(token) {
  let text = String(token ?? '').trim().toLowerCase().replace(/\./g, '')

  if (!text) return null

  const dateTime = /^\d{4}-\d{2}-\d{2}[t ](.+)$/.exec(text)
  if (dateTime) text = dateTime[1].replace(/(z|[+-]\d{2}:?\d{2})$/, '')

  const match = /^(\d{1,2}):(\d{2})(?::\d{2}(?:\d+)?)?\s*(am|pm)?$/.exec(text)
    ?? /^(\d{1,2})h(\d{2})?$/.exec(text)
    ?? /^(\d{1,2})(\d{2})\s*(am|pm)?$/.exec(text)
    ?? /^(\d{1,2})()\s*(am|pm)$/.exec(text)

  if (!match) return null

  let h = Number(match[1])
  const m = Number(match[2] || 0)
  const meridiem = match[3]

  if (meridiem) {
    if (h < 1 || h > 12) return null
    if (meridiem === 'pm' && h < 12) h += 12
    if (meridiem === 'am' && h === 12) h = 0
  }

  if (h > 23 || m > 59) return null

  return `${padDatePart(h)}:${padDatePart(m)}`
}

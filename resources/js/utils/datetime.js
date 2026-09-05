export function padDatePart(n) {
  return String(n).padStart(2, '0')
}

export function isoOf(date) {
  return `${date.getFullYear()}-${padDatePart(date.getMonth() + 1)}-${padDatePart(date.getDate())}`
}

export function parseIso(iso) {
  if (!iso) return null

  const [y, m, d] = iso.split('-').map(Number)

  return new Date(y, m - 1, d)
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

  return addDays(isoOf(date), -offset)
}

export function addDays(iso, n) {
  const date = parseIso(iso)
  date.setDate(date.getDate() + n)

  return isoOf(date)
}

export function sameMonth(a, b) {
  return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth()
}

export function diffDays(isoA, isoB) {
  return Math.round((parseIso(isoB) - parseIso(isoA)) / 86400000)
}

export function isoWeekNumber(date) {
  const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()))
  const dayNum = d.getUTCDay() || 7
  d.setUTCDate(d.getUTCDate() + 4 - dayNum)

  const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1))

  return Math.ceil((((d - yearStart) / 86400000) + 1) / 7)
}

export function resolveLocaleFirstDay(locale) {
  try {
    const info = new Intl.Locale(locale).weekInfo ?? new Intl.Locale(locale).getWeekInfo?.()

    if (info?.firstDay) {
      return info.firstDay % 7
    }
  } catch {
    //
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
    //
  }

  return ['month', 'day', 'year']
}

export function formatEditable(iso, locale) {
  const date = parseIso(iso)
  if (!date) return ''

  return new Intl.DateTimeFormat(locale, { year: 'numeric', month: '2-digit', day: '2-digit' }).format(date)
}

export function parseTypedDate(text, locale) {
  if (!text) return null

  const digits = String(text).match(/\d+/g)
  if (!digits || digits.length < 3) return null

  const order = localeDateOrder(locale)
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

  if (date.getFullYear() !== year || date.getMonth() !== month - 1 || date.getDate() !== day) {
    return null
  }

  return isoOf(date)
}

export function toMinutes(hhmm) {
  const [h, m] = hhmm.split(':').map(Number)

  return h * 60 + m
}

export function parseTimeToken(token) {
  const match = /^(\d{1,2}):(\d{2})$/.exec(String(token).trim())
  if (!match) return null

  const h = Number(match[1])
  const m = Number(match[2])
  if (h > 23 || m > 59) return null

  return `${padDatePart(h)}:${padDatePart(m)}`
}

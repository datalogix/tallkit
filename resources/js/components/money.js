import { debounce, listenWhileConnected } from '../utils'

export function money({ delimiter = ',', thousands = '.', precision = 2, as = 'decimal', model = null, modifiers = '' } = {}) {
  // As text all the way: a float loses digits.
  const toDecimal = (text) => {
    let value = String(text ?? '').trim()

    if (thousands) value = value.split(thousands).join('')
    if (delimiter) value = value.split(delimiter).join('.')

    const negative = value.startsWith('-')
    const [integer = '', fraction = ''] = value.replace(/[^0-9.]/g, '').split('.')
    const digits = integer.replace(/^0+(?=\d)/, '')

    if (digits === '' && fraction === '') return null

    const kept = fraction.slice(0, precision)
    const decimal = kept ? `${digits || '0'}.${kept}` : (digits || '0')

    return negative ? `-${decimal}` : decimal
  }

  // Cents from the digits: 1234.56 * 100 is 123455.99… in float.
  const toValue = (text) => {
    const decimal = toDecimal(text)

    if (decimal === null || as !== 'cents') return decimal

    const [integer, fraction = ''] = decimal.replace('-', '').split('.')
    const digits = (integer + fraction.padEnd(precision, '0')).replace(/^0+(?=\d)/, '')
    const cents = Number(digits)
    const sign = decimal.startsWith('-') ? '-' : ''

    return Number.isSafeInteger(cents) ? (sign ? -cents : cents) : sign + digits
  }

  // As text, rounded half up: a float loses digits past 15.
  const toDisplay = (amount) => {
    if (amount === null || amount === undefined || amount === '') return ''

    const text = typeof amount === 'number'
      ? (Number.isFinite(amount) ? amount.toFixed(Math.min(precision + 2, 20)) : '')
      : String(amount).trim()

    const match = /^(-?)(\d*)(?:\.(\d*))?$/.exec(text)

    if (!match || (match[2] === '' && !match[3])) return ''

    const [, minus, integer, fraction = ''] = match
    let scaled = BigInt((integer || '0') + fraction.slice(0, precision).padEnd(precision, '0'))

    if ((fraction[precision] ?? '0') >= '5') scaled += 1n

    const digits = scaled.toString().padStart(precision + 1, '0')
    const whole = precision ? digits.slice(0, -precision) : digits
    const decimals = precision ? digits.slice(-precision) : ''
    const grouped = whole.replace(/\B(?=(\d{3})+(?!\d))/g, thousands ?? '')

    return `${minus && scaled !== 0n ? '-' : ''}${grouped}${decimals ? delimiter + decimals : ''}`
  }

  const fromValue = (value) => {
    if (as !== 'cents' || value === null || value === '' || value === undefined) return toDisplay(value)

    const match = /^(-?)(\d+)$/.exec(String(value).trim())

    if (!match) return toDisplay(Number(value) / 10 ** precision)

    const digits = match[2].padStart(precision + 1, '0')

    return toDisplay(`${match[1]}${precision ? `${digits.slice(0, -precision)}.${digits.slice(-precision)}` : digits}`)
  }

  const same = (a, b) => (a === null || a === '' || a === undefined)
    ? (b === null || b === '' || b === undefined)
    : Number(a) === Number(b)

  return {
    init() {
      const input = this.$el

      listenWhileConnected(this.$root, input.form, 'formdata', (event) => {
        if (input.name && !input.disabled) event.formData.set(input.name, toValue(input.value) ?? '')
      })

      if (!model || !this.$wire) return

      input.value = fromValue(this.$wire.get(model))

      this.$wire.$watch(model, (value) => {
        if (!same(toValue(input.value), value)) input.value = fromValue(value)
      })

      const live = /\b(live|change)\b/.test(modifiers)
      const event = /\bblur\b/.test(modifiers) ? 'blur' : /\bchange\b/.test(modifiers) ? 'change' : 'input'

      const send = () => {
        const value = toValue(input.value)

        if (!same(value, this.$wire.get(model))) this.$wire.set(model, value, live)
      }

      input.addEventListener(event, live && event === 'input' ? debounce(send, 150) : send)
    },
  }
}

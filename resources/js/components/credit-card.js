import { bind, emit, listenWhileConnected, onFormReset, onLivewireCommit, queryData } from '../utils'

const BRANDS = [
  ['americanexpress', /^3[47]/],
  ['diners', /^3(0[0-5]|[68])/],
  ['jcb', /^35(2[89]|[3-8])/],
  ['discover', /^(6011|65|64[4-9]|622(12[6-9]|1[3-9]\d|[2-8]\d\d|9[01]\d|92[0-5]))/],
  ['unionpay', /^62/],
  ['maestro', /^(5018|5020|5038|5893|6304|6759|676[1-3])/],
  ['mastercard', /^(5[1-5]|2(2[2-9]|[3-6]|7[01]|720))/],
  ['visa', /^4/],
]

const GROUPS = {
  americanexpress: [4, 6, 5],
  diners: [4, 6, 4],
  default: [4, 4, 4, 4],
}

const FIELDS = {
  number: 'cc-number',
  holderName: 'cc-name',
  expirationDate: 'cc-exp',
  cvv: 'cc-csc',
}

export function detectCardBrand(number) {
  const digits = String(number ?? '').replace(/\D/g, '')

  return BRANDS.find(([, pattern]) => pattern.test(digits))?.[0] ?? null
}

function autocompleteField(container, token) {
  return container?.querySelector(`[autocomplete~="${token}"]`) ?? null
}

export function creditCard({ brands = {}, ...options } = {}) {
  return {
    brands,
    back: Boolean(options?.back),
    type: options?.type ?? null,
    values: {
      holderName: options?.holderName ?? null,
      number: options?.number ?? null,
      expirationDate: options?.expirationDate ?? null,
      cvv: options?.cvv ?? null,
    },
    _container: null,
    _stopCommits: null,

    init() {
      const label = this.$root.getAttribute('aria-label')

      bind(this.$root, {
        ['@click']() {
          this.flip()
        },
        [':data-flipped']() {
          return this.back ? '' : null
        },
        [':aria-label']() {
          return [label, this.brandOptions().label].filter(Boolean).join(' ')
        },
      })

      bind(queryData(this.$root, 'credit-card-flip'), {
        [':aria-pressed']() {
          return String(this.back)
        },
      })

      this.connectFields()
    },

    destroy() {
      this._stopCommits?.()
    },

    connectFields() {
      const target = options?.for
      this._container = target
        ? document.getElementById(target) ?? document.querySelector(target)
        : this.$root.closest('form')

      if (!this._container) return

      this.readFields()

      const read = (e) => {
        if (this.fieldKey(e.target)) this.readFields()
      }

      listenWhileConnected(this.$root, this._container, 'input', read)
      listenWhileConnected(this.$root, this._container, 'change', read)
      listenWhileConnected(this.$root, this._container, 'focusin', (e) => {
        const key = this.fieldKey(e.target)
        if (key) this.flip(key === 'cvv')
      })

      onFormReset(this.$root, this._container.closest('form') ?? this._container.querySelector('form'), () => this.readFields())

      this._stopCommits = onLivewireCommit(({ succeed }) => succeed(() => this.$nextTick(() => this.readFields())))
    },

    fieldKey(el) {
      if (!el?.getAttribute) return null

      const tokens = (el.getAttribute('autocomplete') ?? '').split(/\s+/)

      if (tokens.includes('cc-exp-month') || tokens.includes('cc-exp-year')) return 'expirationDate'

      return Object.keys(FIELDS).find((key) => tokens.includes(FIELDS[key])) ?? null
    },

    readFields() {
      const container = this._container

      for (const [key, token] of Object.entries(FIELDS)) {
        const field = autocompleteField(container, token)
        if (field) this.values[key] = field.value || null
      }

      const month = autocompleteField(container, 'cc-exp-month')
      const year = autocompleteField(container, 'cc-exp-year')

      if (month || year) {
        this.values.expirationDate = (month?.value || year?.value)
          ? `${month?.value || 'MM'}/${year?.value || 'YYYY'}`
          : null
      }
    },

    brand() {
      if (this.type) return this.brands[this.type] ? this.type : 'unknown'

      return detectCardBrand(this.values.number) ?? 'unknown'
    },

    brandOptions() {
      return this.brands[this.brand()] ?? this.brands.unknown
    },

    displayNumber() {
      const groups = GROUPS[this.brand()] ?? GROUPS.default
      const number = String(this.values.number ?? '')

      if (!number) return groups.map((size) => '0'.repeat(size)).join(' ')
      if (/[^\d\s]/.test(number)) return number

      const digits = number.replace(/\D/g, '')
      const parts = []
      let start = 0

      for (const size of groups) {
        if (start >= digits.length) break
        parts.push(digits.slice(start, start + size))
        start += size
      }

      if (start < digits.length) parts.push(digits.slice(start))

      return parts.join(' ')
    },

    fit(el, max) {
      this.$nextTick(() => {
        el.removeAttribute('textLength')
        el.removeAttribute('lengthAdjust')

        if (el.getComputedTextLength() <= max) return

        el.setAttribute('textLength', max)
        el.setAttribute('lengthAdjust', 'spacingAndGlyphs')
      })
    },

    update(values = {}) {
      const { back, type, ...rest } = values

      if (type !== undefined) this.type = type

      for (const key of Object.keys(rest)) {
        if (key in this.values) this.values[key] = rest[key]
      }

      if (back !== undefined) this.flip(Boolean(back))
    },

    flip(back = !this.back) {
      if (this.back === back) return

      this.back = back
      emit(this.$root, 'flipped', { back })
    },
  }
}

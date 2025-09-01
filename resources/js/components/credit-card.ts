import { toggleable } from "./toggleable"

const CREDIT_CARD_DEFAULT = {
  opened: true,
  types: [],
  holderName: null,
  number: null,
  type: null,
  expirationDate: null,
  cvv: null
}

export function creditCard(options = {}) {
  const _toggleable = toggleable()

  return {
    ..._toggleable,

    options: CREDIT_CARD_DEFAULT,

    init() {
      _toggleable.init.call(this)
      this.card = this.$data
      this.options = { ...CREDIT_CARD_DEFAULT, ...options }
      this.opened = this.options.opened
    },

    get typeOptions() {
      return this.options.types[this.options.type]
        ? this.options.types[this.options.type]
        : this.options.types.unknown
    },

    update(options = {}) {
      this.options = { ...this.options, ...options }
    },

    flip(isBack = false) {
      if (isBack) {
        this.close()
      } else {
        this.open()
      }
    }
  }
}

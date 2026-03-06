import { bind } from '../utils'
import { toggleable } from './toggleable'

export function creditCard(options = {}) {
  const _toggleable = toggleable()

  return {
    ..._toggleable,

    init() {
      _toggleable.init.call(this)
      this.card = this.$data
      this.options = {
        opened: true,
        types: [],
        holderName: null,
        number: null,
        type: null,
        expirationDate: null,
        cvv: null,
        ...options
      }
      this.opened = this.options.opened

      bind(this.$el, {
        ['@click']() {
          this.toggle()
        },
        [':class']() {
          return {
            'rotate-y-180': !this.isOpened()
          }
        }
      })
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

import { bind } from '../utils'
import { toggleable } from './toggleable'

export function creditCard(options = {}) {
  const _toggleable = toggleable()

  return {
    ..._toggleable,

    options: null,

    init() {
      _toggleable.init.call(this)

      this.options = {
        opened: true,
        types: {},
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
        ['@keydown.enter.prevent']() {
          this.toggle()
        },
        ['@keydown.space.prevent']() {
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

      if ('opened' in options) {
        this.opened = this.options.opened
      }
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

import { bind } from '../utils'
import { toggleable } from '../mixins/toggleable'

export function creditCard(types = {}, options = {}) {
  const _toggleable = toggleable()

  return {
    ..._toggleable,

    types,
    options: {
      opened: true,
      holderName: null,
      number: null,
      type: null,
      expirationDate: null,
      cvv: null,
      ...options,
    },

    init() {
      _toggleable.init.call(this)

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

    typeOptions() {
      return this.types[this.options.type]
        ? this.types[this.options.type]
        : this.types.unknown
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

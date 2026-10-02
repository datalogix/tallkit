import { bind, generateId } from '../utils'
import { toggleable } from '../mixins/toggleable'

export function creditCard(types = {}, options = {}) {
  const _toggleable = toggleable()

  return {
    ..._toggleable,

    types,
    _iconUid: generateId('card-icon'),
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
        [':class']() {
          return {
            'rotate-y-180': !this.isOpened()
          }
        }
      })
    },

    // Ids of its own: shared ids would make two cards share their gradients.
    typeIcon() {
      const uid = this._iconUid

      return (this.typeOptions().icon ?? '')
        .replace(/id="([^"]+)"/g, `id="$1-${uid}"`)
        .replace(/url\(#([^)]+)\)/g, `url(#$1-${uid})`)
        .replace(/href="#([^"]+)"/g, `href="#$1-${uid}"`)
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

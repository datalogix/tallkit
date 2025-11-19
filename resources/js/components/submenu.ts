import { popover } from './popover'
import { bind } from '../utils'

export function submenu() {
  const _popover = popover({ mode: 'manual', position: 'right', align: 'start' })

  return {
    ..._popover,
    _i: null,
    inside: false,

    init() {
      _popover.init.call(this)
      _popover.trigger = this.$root.firstElementChild !== this.popoverElement ? this.$root.firstElementChild : this.$root
      _popover.popoverElement = this.$root.lastElementChild?.matches('[popover]') && this.$root.lastElementChild

      bind(_popover.popoverElement, {
        ['@mouseenter']() {
          this.inside = true
          _popover.trigger.setAttribute('data-active', '')
        },

        ['@mouseleave']() {
          this.inside = false
          this.timerToClose()
        },
      })

      bind(_popover.trigger, {
        ['@click']() {
          this.open()
        },

        ['@mouseenter']() {
          clearTimeout(this._i)
          this.open()
        },

        ['@mouseleave']() {
          this.timerToClose()
        },
      })
    },

    timerToClose() {
      this._i = setTimeout(() => {
        if (! this.inside) {
          this.close()
          _popover.trigger.removeAttribute('data-active')
        }
      }, 10)
    }
  }
}

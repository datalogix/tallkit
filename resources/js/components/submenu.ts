import { bind, popover } from '../utils'

export function submenu() {
  const _popover = popover({ mode: 'manual', position: 'right', align: 'start' })

  return {
    ..._popover,
    _i: null,
    inside: false,

    init() {
      _popover.init.call(this)

      bind(_popover.popover, {
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

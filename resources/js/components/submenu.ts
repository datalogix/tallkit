import { popover } from './popover'
import { bind, isRtl } from '../utils'

export function submenu() {
  const _popover = popover({
    mode: 'manual',
    position: isRtl() ? 'left' : 'right',
    align: 'start'
  })

  return {
    ..._popover,
    _i: null,
    inside: false,

    init() {
      _popover.init.call(this)

      bind(this.popoverElement, {
        ['@mouseenter']() {
          this.inside = true
          this.trigger.setAttribute('data-active', '')
        },

        ['@mouseleave']() {
          this.inside = false
          this.timerToClose()
        },
      })

      bind(this.trigger, {
        ['@click']() {
          this.toggle(false)
        },

        ['@mouseenter']() {
          clearTimeout(this._i)
          this.open(false)
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
          this.trigger.removeAttribute('data-active')
        }
      }, 10)
    },

    destroy() {
      clearTimeout(this._i)
      _popover.destroy.call(this)
    }
  }
}

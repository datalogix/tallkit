import { popover } from './popover'

export function submenu() {
  const _popover = popover({
    mode: 'manual',
    position: 'end',
    align: 'start',
    margin: -4
  })

  return {
    ..._popover,
    _i: null,
    inside: false,

    init() {
      _popover.init.call(this)
    },

    bindPopoverTrigger() {
      _popover.bindPopoverTrigger.call(this)

      const trigger = this.trigger
      const panel = this.popoverElement
      const cleanups = []
      const on = (target, type, handler) => {
        target?.addEventListener(type, handler)
        cleanups.push(() => target?.removeEventListener(type, handler))
      }

      on(panel, 'mouseenter', () => {
        this.inside = true
        this.trigger?.setAttribute('data-active', '')
      })

      on(panel, 'mouseleave', () => {
        this.inside = false
        this.timerToClose()
      })

      on(trigger, 'click', () => this.toggle(false))

      on(trigger, 'mouseenter', () => {
        clearTimeout(this._i)
        this.open(false)
      })

      on(trigger, 'mouseleave', () => this.timerToClose())

      const unbindBase = this._unbindTrigger

      this._unbindTrigger = () => {
        unbindBase?.()
        cleanups.forEach((cleanup) => cleanup())
      }
    },

    timerToClose() {
      clearTimeout(this._i)

      this._i = setTimeout(() => {
        if (! this.inside) {
          this.close()
          this.trigger?.removeAttribute('data-active')
        }
      }, 100)
    },

    destroy() {
      clearTimeout(this._i)
      _popover.destroy.call(this)
    }
  }
}

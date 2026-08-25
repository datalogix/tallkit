import { bind } from '../utils'
import { toggleable } from '../mixins/toggleable'
import { sticky as stickyComponent } from '../mixins/sticky'

export function sidebar(name?: string, sticky?: boolean, stashable?: boolean) {
  const _toggleable = toggleable()
  const _sticky = stickyComponent()

  return {
    ..._toggleable,
    ..._sticky,

    init() {
      _toggleable.init.call(this)

      if (sticky) {
        _sticky.init.call(this)
      }

      if (stashable) {
        this.$el.removeAttribute('data-mobile-cloak')
        this.screenLg = window.innerWidth >= 1024

        bind(this.$el, {
          [':data-stashed']() {
            return !this.screenLg
          },

          ['x-resize.document']() {
            this.screenLg = window.innerWidth >= 1024
          },

          [`@sidebar-${name ?? ''}-close.window`]() {
            this.close()
          },

          [`@sidebar-${name ?? ''}-toggle.window`]() {
            this.toggle()
          },

          ['@keydown.escape.window']() {
            if (this.isOpened()) this.close()
          },
        })

        this._dispatchState()
      }
    },

    open() {
      this.$el.setAttribute('data-show-stashed-sidebar', '')
      _toggleable.open.call(this)
      this._dispatchState()
    },

    close() {
      this.$el.removeAttribute('data-show-stashed-sidebar')
      _toggleable.close.call(this)
      this._dispatchState()
    },

    _dispatchState() {
      window.dispatchEvent(new CustomEvent(`sidebar-${name ?? ''}-state`, { detail: { opened: this.opened } }))
    },

    destroy() {
      if (sticky) {
        _sticky.destroy.call(this)
      }
    },
  }
}

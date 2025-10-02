import { bind } from "../utils"
import { toggleable } from "./toggleable"
import { sticky as stickyComponent } from "./sticky"

export function sidebar(name?: string, sticky?: boolean, stashable?: boolean) {
  const _toggleable = toggleable()
  const _sticky = stickyComponent()

  return {
    ..._toggleable,

    init() {
      _toggleable.init.call(this)

      if (sticky) {
        _sticky.init.call(this)
      }

      if (stashable) {
        this.$el.removeAttribute('data-mobile-cloak');
        this.screenLg = window.innerWidth >= 1024

        bind(this.$el, {
          ['x-bind:data-stashed']() {
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
        })
      }
    },

    open() {
      this.$el.setAttribute('data-show-stashed-sidebar', '')
      _toggleable.open.call(this)
    },

    close() {
      this.$el.removeAttribute('data-show-stashed-sidebar')
      _toggleable.close.call(this)
    },
  }
}

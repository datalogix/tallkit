import { dataSelector, bind, emit, eventName, isEscapeHandled, FOCUSABLE, isRendered } from '../utils'
import { toggleable } from '../mixins/toggleable'
import { stickable } from '../mixins/stickable'

export function sidebar(name, sticky, stashable) {
  const _toggleable = toggleable()
  const _sticky = stickable()

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

          // Moved off screen is still reachable by Tab and screen readers: inert while closed.
          [':inert']() {
            return !this.screenLg && !this.isOpened()
          },

          ['x-resize.document']() {
            this.screenLg = window.innerWidth >= 1024
          },

          [`@${eventName('sidebar-close')}.window`](event) {
            if ((event.detail?.name ?? null) === (name ?? null)) this.close()
          },

          [`@${eventName('sidebar-toggle')}.window`](event) {
            if ((event.detail?.name ?? null) === (name ?? null)) this.toggle()
          },

          ['@keydown.escape.window'](event) {
            if (this.isOpened() && !isEscapeHandled(event)) this.close()
          },
        })

        this._dispatchState()
      }
    },

    open() {
      const wasOpened = this.isOpened()

      this.$el.setAttribute('data-show-stashed-sidebar', '')
      _toggleable.open.call(this)
      this._dispatchState()

      if (!wasOpened) this._focusInside()
    },

    close() {
      const wasOpened = this.isOpened()

      this.$el.removeAttribute('data-show-stashed-sidebar')
      _toggleable.close.call(this)
      this._dispatchState()

      if (wasOpened) this._focusBack()
    },

    _focusInside() {
      if (!stashable || this.screenLg) return

      this._returnFocus = document.activeElement

      this.$nextTick(() => {
        const first = Array.from(this.$el.querySelectorAll(FOCUSABLE)).find(isRendered)

        if (first) {
          first.focus()
          return
        }

        if (!this.$el.hasAttribute('tabindex')) this.$el.setAttribute('tabindex', '-1')
        this.$el.focus()
      })
    },

    _focusBack() {
      if (!stashable || this.screenLg) return

      const active = document.activeElement

      if (active && active !== document.body && !this.$el.contains(active)) return

      const target = this._returnFocus?.isConnected && this._returnFocus !== document.body
        ? this._returnFocus
        : document.querySelector(`${dataSelector('sidebar-toggle', name ?? '')} button, ${dataSelector('sidebar-toggle', name ?? '')}`)

      this._returnFocus = null
      target?.focus?.()
    },

    _dispatchState() {
      emit(window, eventName('sidebar-state'), { name: name ?? null, opened: this.opened })
    },

    destroy() {
      if (sticky) {
        _sticky.destroy.call(this)
      }
    },
  }
}

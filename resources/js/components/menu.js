import { dataSelector, queryAllData, bind } from '../utils'

export function menu() {
  return {
    observer: null,
    _current: null,
    typed: '',
    typedTimeout: null,

    init() {
      const menu = this.$el

      const itemOf = (event) => {
        const item = event.target instanceof Element ? event.target.closest(dataSelector('menu-item')) : null

        return item && item.closest(dataSelector('menu')) === menu && !item.disabled ? item : null
      }

      const activate = (item) => {
        this.menuItems().forEach((other) => {
          if (other !== item) other.removeAttribute('data-active')
        })

        item.setAttribute('data-active', '')
      }

      bind(menu, {
        ['@mouseover'](event) {
          const item = itemOf(event)

          if (item) activate(item)
        },

        ['@mouseout'](event) {
          const item = itemOf(event)

          if (!item || item.contains(event.relatedTarget)) return

          item.removeAttribute('data-active')

          const focused = this.menuItems().find((other) => other === document.activeElement)

          if (focused) activate(focused)
        },

        ['@focusin'](event) {
          const item = itemOf(event)

          if (!item) return

          activate(item)
          this.syncTabindex(item)
        },

        ['@focusout'](event) {
          const item = event.target instanceof Element ? event.target.closest(dataSelector('menu-item')) : null

          if (item && item.closest(dataSelector('menu')) === menu) item.removeAttribute('data-active')

          const to = event.relatedTarget
          if (to instanceof Element && !menu.contains(to) && typeof this.close === 'function' && this.isOpened?.()) {
            this.close()
          }
        },

        ['@keydown.arrow-down.prevent']() {
          this.focusItem(this.menuItems(), 1)
        },

        ['@keydown.arrow-up.prevent']() {
          this.focusItem(this.menuItems(), -1)
        },

        ['@keydown.home.prevent']() {
          this.focusItem(this.menuItems(), 'first')
        },

        ['@keydown.end.prevent']() {
          this.focusItem(this.menuItems(), 'last')
        },

        ['@keydown'](event) {
          if (event.key.length !== 1 || event.ctrlKey || event.metaKey || event.altKey || event.key === ' ') return
          if (event.target.closest?.('input, textarea, select, [contenteditable]')) return

          this.typeAhead(event.key)
        },
      })

      if (typeof this.isOpened !== 'function') {
        menu.closest('[popover]')?.removeAttribute('popover')
      }

      this.syncTabindex()
      this.observer = new MutationObserver(() => this.syncTabindex())
      this.observer.observe(menu, { childList: true, subtree: true, attributes: true, attributeFilter: ['tabindex'] })
    },

    destroy() {
      this.observer?.disconnect()
      clearTimeout(this.typedTimeout)
    },

    syncTabindex(active = null) {
      const items = this.menuItems()
      const usable = (item) => item && items.includes(item) && !item.disabled
      const current = active
        ?? (usable(this._current) ? this._current : null)
        ?? items.find((item) => item.getAttribute('tabindex') === '0' && !item.disabled)
        ?? items.find((item) => !item.disabled)

      this._current = current

      items.forEach((item) => {
        const value = item === current ? '0' : '-1'

        if (item.getAttribute('tabindex') !== value) item.setAttribute('tabindex', value)
      })
    },

    typeAhead(key) {
      clearTimeout(this.typedTimeout)
      this.typed += key.toLowerCase()
      this.typedTimeout = setTimeout(() => { this.typed = '' }, 500)

      const enabled = this.menuItems().filter((item) => !item.disabled)
      const start = enabled.indexOf(document.activeElement)
      const from = this.typed.length === 1 ? start + 1 : Math.max(start, 0)
      const ordered = [...enabled.slice(from), ...enabled.slice(0, from)]
      const match = ordered.find((item) => item.textContent.trim().toLowerCase().startsWith(this.typed))

      match?.focus()
    },

    menuItems() {
      return queryAllData(this.$root, 'menu-item')
        .filter((item) => item.closest(dataSelector('menu')) === this.$root)
    },

    focusItem(items, direction) {
      const enabled = items.filter((item) => !item.disabled)
      if (!enabled.length) return

      const currentIndex = enabled.indexOf(document.activeElement)
      let index

      if (direction === 'first') {
        index = 0
      } else if (direction === 'last') {
        index = enabled.length - 1
      } else if (currentIndex === -1) {
        index = direction === 1 ? 0 : enabled.length - 1
      } else {
        index = (currentIndex + direction + enabled.length) % enabled.length
      }

      enabled[index].focus()
    }
  }
}

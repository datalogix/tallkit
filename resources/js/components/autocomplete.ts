import { bind, normalize, timeout } from '../utils'
import { popover } from './popover'

export function autocomplete() {
  const _popover = popover({ mode: 'manual', position: 'bottom', align: 'start' })

  return {
    ..._popover,
    current: null,

    get input() {
      return this.$root.querySelector('[data-tallkit-autocomplete]')
    },

    get items() {
      return Array.from(
        this.$root.querySelectorAll('[data-tallkit-autocomplete-item-container]:has([data-tallkit-button-content])')
      ) as HTMLLIElement[]
    },

    get filteredItems() {
      return this.items.filter(item => {
        if (item.hasAttribute('data-hidden')) return false

        const button = item.querySelector('[data-tallkit-autocomplete-item]')

        return !button?.hasAttribute('disabled')
      })
    },

    setPosition() {
      _popover.setPosition.call(this);
      this.popoverElement.style.minWidth = `${this.input.offsetWidth}px`;
    },

    init() {
      _popover.init.call(this);
      _popover.trigger = this.input;
      _popover.popoverElement = this.$root.lastElementChild?.matches('[popover]') && this.$root.lastElementChild;

      bind(this.$root.querySelectorAll('[data-tallkit-autocomplete-item]'), (element) => ({
        ['@click']: () => this.filteredItems.forEach((item, index) => {
          if (item.querySelector('[data-tallkit-autocomplete-item]') === element) {
            this.current = index
            this.selectActive()
            return
          }
        }),

        ['@mouseenter']: () => this.filteredItems.forEach((item, index) => {
          if (item.querySelector('[data-tallkit-autocomplete-item]') === element) {
            this.setActive(index)
            this.$dispatch('autocomplete-item-hover', { index, item })
            return
          }
        }),
      }))

      bind(this.input, {
        ['@input']: () => {
          this.$dispatch('autocomplete-search-updated', { query: this.input.value })
          this.search()

          if (this.filteredItems.length === 0) {
            this.close()
          } else {
            this.open()
          }
        },
        ['@focus']: (e) => {
          if (this.filteredItems.length === 0) {
            this.close()
          } else {
            this.open()
            this.setActive()
          }
        },
        ['@blur']: () => {
          this.clearActive()
          timeout(() => this.close(), 100)
        },
        ['@keydown.enter.prevent']: () => this.selectActive(),
        ['@keydown.arrow-down.prevent']: () => this.next(),
        ['@keydown.arrow-up.prevent']: () => this.prev(),
        ['@keyup.escape.window']: () =>  this.close(),
      })

      this.$nextTick(() => {
        this.search()
        this.clearActive()
      })

      this.$dispatch('autocomplete-initialized')
    },

    clearActive() {
      this.items.forEach(item => {
        item.querySelector('[data-tallkit-autocomplete-item]')?.removeAttribute('data-active')
      })
      this.current = null
    },

    prev() {
      if (!this.popoverElement?.matches(':popover-open')) {
        this.open()
        return
      }

      if (this.current == null) return

      this.setActive((this.current - 1 + this.filteredItems.length) % this.filteredItems.length)
    },

    next() {
      if (!this.popoverElement?.matches(':popover-open')) {
        this.open()
        return
      }

      if (this.current == null) return

      this.setActive((this.current + 1) % this.filteredItems.length)
    },

    search() {
      const normalizeOptions = {
        lowercase: true,
        replaceAccents: true,
        removeSpaces: true,
      }

      const value = normalize(this.input.value, normalizeOptions)

      this.clearItems()

      if (value) {
        this.items.forEach(item => {
          const span = item.querySelector('[data-tallkit-button-content]')
          const content = normalize(span?.textContent, normalizeOptions) || ''

          if (!content.includes(value)) {
            item.setAttribute('data-hidden', '')
          }
        })
      }

      this.$dispatch('autocomplete-items-changed', {
        items: this.filteredItems.length,
      })

      this.setActive()
    },

    clearItems() {
      this.items.forEach(item => {
        item.querySelector('[data-tallkit-autocomplete-item]')?.removeAttribute('data-active')
        item.removeAttribute('data-hidden')
      })
    },

    setActive(index = 0) {
      const items = this.filteredItems
      if (index < 0 || index >= items.length) return

      if (this.current !== null) {
        const last = items.at(this.current)
        last?.querySelector('[data-tallkit-autocomplete-item]')?.removeAttribute('data-active')
      }

      const item = items.at(index)
      if (!item) return

      const button = item.querySelector('[data-tallkit-autocomplete-item]')
      if (button?.hasAttribute('disabled')) return

      button?.setAttribute('data-active', '')
      this.current = index

      item.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
      })

      this.$dispatch('autocomplete-active-changed', { index, item, button })
    },

    selectActive() {
      if (this.current === null) return

      const item = this.filteredItems.at(this.current)
      if (!item) return

      const button = item.querySelector('[data-tallkit-autocomplete-item]')
      if (!button || button.hasAttribute('disabled')) return

     // button.dispatchEvent(new Event('click', { bubbles: true }))

      this.input.value = button.querySelector('[data-tallkit-button-content]')?.textContent.trim() || ''
      this.input.dispatchEvent(new Event('input', { bubbles: true }))
      this.input.dispatchEvent(new Event('change', { bubbles: true }))
      this.close()

      this.$dispatch('autocomplete-item-selected', {
        index: this.current,
        item,
        button,
      })
    }
  }
}

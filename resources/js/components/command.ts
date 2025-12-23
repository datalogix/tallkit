import { bind, fuzzyMatch, normalize } from '../utils'

export function command() {
  return {
    current: null,

    get input() {
      return this.$root.querySelector('[data-tallkit-command-input]')
    },

    get items() {
      return Array.from(
        this.$root.querySelectorAll('[data-tallkit-command-item-container]:has([data-tallkit-button-content])')
      ) as HTMLLIElement[]
    },

    get filteredItems() {
      return this.items.filter(item => {
        if (item.hasAttribute('data-hidden')) return false

        const button = item.querySelector('[data-tallkit-command-item]')

        return !button?.hasAttribute('disabled')
      })
    },

    get noRecords() {
      return this.$root.querySelector('[data-tallkit-command-no-records]')
    },

    init() {
      bind(this.$root, {
        ['@mouseleave']: () => this.clearActive()
      })

      bind(this.$root.querySelectorAll('[data-tallkit-command-item]'), (element) => ({
        ['@mouseenter']: () => this.filteredItems.forEach((item, index) => {
          if (item.querySelector('[data-tallkit-command-item]') === element) {
            this.setActive(index)
            this.$dispatch('command-item-hover', { index, item })
            return
          }
        }),
      }))

      bind(this.input, {
        ['@input']: () => {
          this.$dispatch('command-search-updated', { query: this.input.value })
          this.search()
        },
        ['@focus']: (e) => {
          this.setActive()
        },
        ['@blur']: () => this.clearActive(),
        ['@keydown.enter.prevent']: () => this.selectActive(),
        ['@keydown.arrow-down.prevent']: () => this.next(),
        ['@keydown.arrow-up.prevent']: () => this.prev(),
      })

      this.$nextTick(() => {
        this.search()
        this.clearActive()
      })

      this.$dispatch('command-initialized')
    },

    clearActive() {
      this.items.forEach(item => {
        item.querySelector('[data-tallkit-command-item]')?.removeAttribute('data-active')
      })
      this.current = null
    },

    prev() {
      if (this.current == null) return

      this.setActive((this.current - 1 + this.filteredItems.length) % this.filteredItems.length)
    },

    next() {
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

          if (!fuzzyMatch(content, value)) {
            item.setAttribute('data-hidden', '')
          }
        })
      }

      this.$dispatch('command-items-changed', {
        items: this.filteredItems.length,
      })

      this.toggleNoRecords()
      this.setActive()
    },

    clearItems() {
      this.items.forEach(item => {
        item.querySelector('[data-tallkit-command-item]')?.removeAttribute('data-active')
        item.removeAttribute('data-hidden')
      })
    },

    toggleNoRecords() {
      if (!this.noRecords) return

      if (this.filteredItems.length === 0) {
        this.noRecords.removeAttribute('hidden')
      } else {
        this.noRecords.setAttribute('hidden', '')
      }
    },

    setActive(index = 0) {
      const items = this.filteredItems
      if (index < 0 || index >= items.length) return

      if (this.current !== null) {
        const last = items.at(this.current)
        last?.querySelector('[data-tallkit-command-item]')?.removeAttribute('data-active')
      }

      const item = items.at(index)
      if (!item) return

      const button = item.querySelector('[data-tallkit-command-item]')
      if (button?.hasAttribute('disabled')) return

      button?.setAttribute('data-active', '')
      this.current = index

      item.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
      })

      this.$dispatch('command-active-changed', { index, item, button })
    },

    selectActive() {
      if (this.current === null) return

      const item = this.filteredItems.at(this.current)
      if (!item) return

      const button = item.querySelector('[data-tallkit-command-item]')
      if (!button || button.hasAttribute('disabled')) return

      button.dispatchEvent(new Event('click', { bubbles: true }))

      this.$dispatch('command-item-selected', {
        index: this.current,
        item,
        button,
      })
    }
  }
}

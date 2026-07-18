import Fuse from 'fuse.js'
import { bind, normalize } from '../utils'

export function command({ hideEmpty = false, clearOnSelect = false, ...fuseOptions } = {}) {
  return {
    input: null,
    list: null,
    noRecords: null,

    items: [],
    filteredItems: [],

    index: null,
    fuse: null,
    lastInteraction: null,

    init() {
      this.input = this.$root.querySelector('[data-tallkit-input]')
      this.list = this.$root.querySelector('[role=listbox]')
      this.noRecords = this.$root.querySelector('[role=status]')

      this.refreshItems()

      this.$watch(() => this.index, (index) => {
        this.setActive(index)
      })

      bind(this.input, {
        ['@input']() {
          this.lastInteraction = 'keyboard'
          this.$dispatch('command-search-updated', { query: this.input.value })
          this.search()
        },

        ['@focus']() {
          this.search()
        },

        ['@blur']() {
          this.clear()
        },

        ['@keydown.esc.prevent']() {
          this.clear()
        },

        ['@keydown.arrow-up.prevent']() {
          this.lastInteraction = 'keyboard'
          this.prev()
        },

        ['@keydown.arrow-down.prevent']() {
          this.lastInteraction = 'keyboard'
          this.next()
        },

        ['@keydown.enter.prevent']() {
          this.select(this.index)
        },

        ['@keydown.tab']() {
          this.select(this.index)
        }
      })

      bind(this.list, {
        ['@mouseleave']: () => this.clear(),

        ['@mousedown']: (e) => {
          const item = e.target.closest('[role=option]')
          if (!item) return

          const index = Number(item.dataset.index)

          if (!Number.isNaN(index)) {
            this.select(index)
          }
        },

        ['@mousemove']: (e) => {
          if (
            this.lastInteraction === 'keyboard' &&
            e.movementX === 0 &&
            e.movementY === 0
          ) {
            return
          }

          this.lastInteraction = 'mouse'

          const item = e.target.closest('[role=option]')
          if (!item) return

          const index = Number(item.dataset.index)

          if (Number.isNaN(index)) return

          if (this.index !== index) {
            this.index = index
          }
        },
      })

      this.$nextTick(() => {
        this.search()
        this.$dispatch('command-initialized')
      })
    },

    refreshItems() {
      this.items = Array.from(
        this.list.querySelectorAll('[role=option]')
      ).map((item) => {
        item.hidden = true

        return {
          title: normalize(item.querySelector('[data-item-content]').textContent, { removeSpaces: true }),
          el: item.firstElementChild,
          li: item,
        }
      })

      const fuseIndex = Fuse.createIndex(['title'], this.items)

      this.fuse = new Fuse(
        this.items,
        {
          ignoreDiacritics: true,
          includeScore: true,
          threshold: 0.1,
          keys: ['title'],
          ...fuseOptions,
        },
        fuseIndex
      )
    },

    search() {
      let query = this.input.value.trim()
      this.clear()

      this.items.forEach(item => {
        item.li.hidden = true
      })

      const fragment = document.createDocumentFragment()
      let results = []

      if (query) {
        results = this.fuse.search(query)
      } else if (! hideEmpty) {
        results = this.items.map(item => ({ item }))
      }

      this.filteredItems = results.map((result, index) => {
        const li = result.item.li

        li.hidden = false
        li.dataset.index = index

        fragment.appendChild(li)

        return result.item
      })

      this.list.appendChild(fragment)

      this.$dispatch('command-items-changed', {
        list: this.list,
        items: this.items,
        filteredItems: this.filteredItems,
      })

      if (this.filteredItems.length && query) {
        this.$nextTick(() => {
          this.index = 0
        })
      }

      this.toggleNoRecords()
    },

    prev() {
      if (this.filteredItems.length === 0) return
      this.index = this.index === null ? this.filteredItems.length - 1 : (this.index - 1 + this.filteredItems.length) % this.filteredItems.length
    },

    next() {
      if (this.filteredItems.length === 0) return
      this.index = this.index === null ? 0 : (this.index + 1) % this.filteredItems.length
    },

    select(index) {
      if (index === null) return

      const item = this.filteredItems[index]
      if (!item) return

      const button = item.el
      if (!button || button.hasAttribute('disabled')) return

      button.dispatchEvent(new Event('click', { bubbles: true }))

      if (clearOnSelect) {
        this.input.value = ''
        this.input.dispatchEvent(new Event('input', { bubbles: true }))
        this.input.dispatchEvent(new Event('change', { bubbles: true }))
      }

      this.$dispatch('command-item-selected', { index, item, button })
    },

    setActive(index: number) {
      this.clearActive()

      const item = this.filteredItems[index]
      if (!item) return

      item.el.dataset.active = 'true'

      item.li.scrollIntoView({
        block: 'nearest',
      })

      this.$dispatch('command-active-changed', { index, item })
    },

    clearActive() {
      this.filteredItems.forEach(item => {
        delete item.el.dataset.active
      })
    },

    clear() {
      this.clearActive()
      this.index = null
    },

    toggleNoRecords() {
      if (!this.noRecords) return

      if (this.filteredItems.length === 0 && (this.input.value && !hideEmpty)) {
        this.noRecords.removeAttribute('hidden')
      } else {
        this.noRecords.setAttribute('hidden', '')
      }
    },
  }
}

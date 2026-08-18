import Fuse from 'fuse.js'
import { dataKey, bind, debounce, normalize, setFieldValue } from '../utils'

export function listbox({ hideEmpty = false, clearOnSelect = false, ...fuseOptions } = {}) {
  return {
    input: null,
    list: null,
    noRecords: null,

    items: [],
    filteredItems: [],

    index: null,
    fuse: null,
    lastInteraction: null,
    debouncedSearch: null,

    init() {
      this.input = this.$root.querySelector(dataKey('input'))
      this.list = this.$root.querySelector('[role=listbox]')
      this.noRecords = this.$root.querySelector('[role=status]')

      this.refreshItems()

      this.$watch(() => this.index, (index) => {
        this.setActive(index)
      })

      this.debouncedSearch = debounce(() => this.search(), 150)

      bind(this.input, {
        ['@input']() {
          this.lastInteraction = 'keyboard'
          this.$dispatch('listbox-search-updated', { query: this.input.value })
          this.debouncedSearch()
        },

        ['@focus']() {
          this.search()
        },

        ['@blur']() {
          this.clear()
        },

        ['@keydown.escape.prevent']() {
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

        ['@keydown.home.prevent']() {
          this.lastInteraction = 'keyboard'
          this.first()
        },

        ['@keydown.end.prevent']() {
          this.lastInteraction = 'keyboard'
          this.last()
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
          if (this.isDisabled(this.filteredItems[index])) return

          if (this.index !== index) {
            this.index = index
          }
        },

        ['@keydown.escape.prevent']() {
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

        ['@keydown.home.prevent']() {
          this.lastInteraction = 'keyboard'
          this.first()
        },

        ['@keydown.end.prevent']() {
          this.lastInteraction = 'keyboard'
          this.last()
        },

        ['@keydown.enter.prevent']() {
          this.select(this.index)
        },

        ['@keydown.space.prevent']() {
          this.select(this.index)
        },
      })

      this.$nextTick(() => {
        this.search()
        this.$dispatch('listbox-initialized')
      })
    },

    refreshItems() {
      this.items = Array.from(
        this.list.querySelectorAll('[role=option]')
      ).map((item) => {
        item.hidden = true

        if (item?.firstElementChild?.disabled) {
          item.setAttribute('aria-disabled', 'true')
        }

        return {
          title: normalize(item.querySelector('[data-item-content]')?.textContent, { removeSpaces: true }),
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
      const query = this.input ? this.input.value.trim() : ''
      this.clear()

      if (!query.length && hideEmpty) {
        this.filteredItems = []
        return
      }

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

      this.$dispatch('listbox-items-changed', {
        list: this.list,
        items: this.items,
        filteredItems: this.filteredItems,
      })

      if (this.filteredItems.length && query.length) {
        this.$nextTick(() => {
          this.index = 0
        })
      }

      this.toggleNoRecords()
    },

    isDisabled(item) {
      return !!item?.el?.hasAttribute('disabled')
    },

    prev() {
      if (this.filteredItems.length === 0) return

      let index = this.index === null ? this.filteredItems.length - 1 : (this.index - 1 + this.filteredItems.length) % this.filteredItems.length

      for (let i = 0; i < this.filteredItems.length && this.isDisabled(this.filteredItems[index]); i++) {
        index = (index - 1 + this.filteredItems.length) % this.filteredItems.length
      }

      if (this.isDisabled(this.filteredItems[index])) return

      this.index = index
    },

    next() {
      if (this.filteredItems.length === 0) return

      let index = this.index === null ? 0 : (this.index + 1) % this.filteredItems.length

      for (let i = 0; i < this.filteredItems.length && this.isDisabled(this.filteredItems[index]); i++) {
        index = (index + 1) % this.filteredItems.length
      }

      if (this.isDisabled(this.filteredItems[index])) return

      this.index = index
    },

    first() {
      if (this.filteredItems.length === 0) return

      let index = 0

      while (index < this.filteredItems.length && this.isDisabled(this.filteredItems[index])) {
        index++
      }

      if (index >= this.filteredItems.length) return

      this.index = index
    },

    last() {
      if (this.filteredItems.length === 0) return

      let index = this.filteredItems.length - 1

      while (index >= 0 && this.isDisabled(this.filteredItems[index])) {
        index--
      }

      if (index < 0) return

      this.index = index
    },

    select(index: number | null) {
      if (index === null) return

      const item = this.filteredItems[index]
      if (!item) return

      const button = item.el
      if (!button || button.hasAttribute('disabled')) return

      button.dispatchEvent(new Event('click', { bubbles: true }))

      if (clearOnSelect) {
        setFieldValue(this.input, '')
      }

      this.$dispatch('listbox-item-selected', { index, item, button })
    },

    setActive(index: number) {
      this.clearActive()

      const item = this.filteredItems[index]
      if (!item) return

      item.el.dataset.active = 'true'
      item.li.setAttribute('aria-selected', 'true')

      if (item.li.hasAttribute('id')) {
        this.list.setAttribute('aria-activedescendant', item.li.getAttribute('id'))
      }

      item.li.scrollIntoView({
        block: 'nearest',
      })

      this.$dispatch('listbox-active-changed', { index, item })
    },

    clearActive() {
      this.filteredItems.forEach(item => {
        delete item.el.dataset.active
        item.li.removeAttribute('aria-selected')
      })

      this.list.removeAttribute('aria-activedescendant')
    },

    clear() {
      this.debouncedSearch?.cancel()
      this.clearActive()
      this.index = null
    },

    toggleNoRecords() {
      if (!this.noRecords) return

      if (this.filteredItems.length === 0 && (this.input?.value && !hideEmpty)) {
        this.noRecords.removeAttribute('hidden')
        this.list.setAttribute('hidden', '')
      } else {
        this.noRecords.setAttribute('hidden', '')
        this.list.removeAttribute('hidden')
      }
    },
  }
}

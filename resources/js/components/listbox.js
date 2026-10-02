import Fuse from 'fuse.js'
import { queryData, bind, debounce, normalizeText, setFieldValue, onLivewireCommit, generateId, emit, toNumber } from '../utils'

export function listbox({ hideEmpty = false, clearOnSelect = false, autoHighlight = true, tabSelects = true, ...fuseOptions } = {}) {
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
    // Its own name: the combobox and the autocomplete are popovers too, whose hook is livewireCommitCleanup.
    listboxCommitCleanup: null,
    _itemsByElement: null,

    init() {
      this.input = queryData(this.$root, 'input')
      this.list = this.$root.querySelector('[role=listbox]')
      this.noRecords = queryData(this.$root, 'listbox-no-records')

      this.refreshItems()

      this.listboxCommitCleanup = onLivewireCommit(({ component, succeed }) => {
        succeed(() => {
          if (!this.$root?.isConnected) return

          if (component?.el && !component.el.contains(this.$root)) return

          this.refreshItems()
          this.search()
        })
      })

      this.$watch(() => this.index, (index) => {
        this.setActive(index)
      })

      this.debouncedSearch = debounce(() => this.search(), 150)

      bind(this.input, {
        ['@input']() {
          this.lastInteraction = 'keyboard'
          emit(this.$root, 'searched', { query: this.input.value })
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

        // No Home/End: in a text field they move the caret.

        // Only with an option highlighted: otherwise Enter submits the form, as in any field.
        ['@keydown.enter'](e) {
          if (this.index === null || !this.filteredItems[this.index]) return

          e.preventDefault()
          this.select(this.index)
        },

        ['@keydown.tab']() {
          if (tabSelects) this.select(this.index)
        }
      })

      bind(this.list, {
        ['@mouseleave']: () => this.clear(),

        // On mousedown, not click: the field's blur would close the list first.
        ['@mousedown']: (e) => {
          const item = (e.target).closest('[role=option]')
          if (!item) return

          const index = toNumber(item.dataset.index)

          if (index !== null) {
            this.select(index)
          }
        },

        ['@mousemove']: (e) => {
          // A mousemove with no movement is the list scrolling under the pointer.
          if (
            this.lastInteraction === 'keyboard' &&
            e.movementX === 0 &&
            e.movementY === 0
          ) {
            return
          }

          this.lastInteraction = 'mouse'

          const item = (e.target).closest('[role=option]')
          if (!item) return

          const index = toNumber(item.dataset.index)

          if (index === null) return
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
        emit(this.$root, 'ready')
      })
    },

    destroy() {
      this.listboxCommitCleanup?.()
    },

    refreshItems() {
      const items = Array.from(
        this.list.querySelectorAll('[role=option]')
      ).map((item) => {
        item.hidden = true

        if (item?.firstElementChild?.hasAttribute('disabled')) {
          item.setAttribute('aria-disabled', 'true')
        } else {
          item.removeAttribute('aria-disabled')
        }

        return {
          title: normalizeText(item.querySelector('[data-item-content]')?.textContent, { removeSpaces: true }),
          el: item.firstElementChild,
          li: item,
        }
      })

      const key = (item) => `${item.title}\u0000${item.li.hasAttribute('aria-disabled')}`
      const previous = this._itemsByElement

      if (this.fuse && previous && previous.size === items.length
        && items.every((item) => previous.get(item.li) === key(item))) {
        return
      }

      this.items = items
      this._itemsByElement = new Map(items.map((item) => [item.li, key(item)]))

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

      this.items.forEach((item) => {
        item.li.hidden = true
      })

      const fragment = document.createDocumentFragment()
      let results = []

      if (query) {
        results = this.fuse.search(query)
      } else if (! hideEmpty) {
        results = this.items.map((item) => ({ item }))
      }

      this.filteredItems = results.map((result, index) => {
        const li = result.item.li

        li.hidden = false
        li.dataset.index = String(index)

        fragment.appendChild(li)

        return result.item
      })

      this.list.appendChild(fragment)

      emit(this.$root, 'filtered', {
        list: this.list,
        items: this.items,
        filteredItems: this.filteredItems,
      })

      if (autoHighlight && this.filteredItems.length && query.length) {
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

    select(index) {
      if (index === null) return

      const item = this.filteredItems[index]
      if (!item) return

      const button = item.el
      if (!button || button.hasAttribute('disabled')) return

      button.dispatchEvent(new Event('click', { bubbles: true }))

      if (clearOnSelect) {
        setFieldValue(this.input, '')
      }

      emit(this.$root, 'selected', { index, item, button })
    },

    setActive(index) {
      this.clearActive()

      if (index === null) return

      const item = this.filteredItems[index]
      if (!item) return

      // aria-selected is what is chosen: the highlighted one is told by aria-activedescendant.
      item.el.dataset.active = 'true'

      if (!item.li.id) item.li.id = generateId('listbox-option')

      this.list.setAttribute('aria-activedescendant', item.li.id)
      this.input?.setAttribute('aria-activedescendant', item.li.id)

      item.li.scrollIntoView({
        block: 'nearest',
      })

      emit(this.$root, 'highlighted', { index, item })
    },

    clearActive() {
      this.filteredItems.forEach((item) => {
        delete item.el.dataset.active
      })

      this.list.removeAttribute('aria-activedescendant')
      this.input?.removeAttribute('aria-activedescendant')
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

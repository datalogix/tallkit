import Fuse from 'fuse.js'
import { bind } from '../utils'
import { popover } from './popover'

export function autocomplete(options = {}) {
  const _popover = popover({ mode: 'manual', position: 'bottom', align: 'start' })

  return {
    ..._popover,
    uid: crypto?.randomUUID?.() || Math.random().toString(36).slice(2,8),

    abortController: null,
    prefetchController: null,
    scrollBehavior: options.scrollBehavior ?? 'auto',

    useCache: options.cache ?? true,
    usePagination: options.pagination ?? true,
    useVirtualization: options.virtualization ?? true,
    fuseOptions: options.fuseOptions || {},
    highlightMatches: options.highlightMatches ?? true,

    state: 'idle',
    query: '',
    selected: null,
    current: null,
    _renderStatesRAF: null,

    _items: [],
    _filtered: [],
    _itemsVersion: 0,
    fuse: null,
    lastQuery: '',

    minLength: options.minLength || 1,
    delay: options.delay || 300,
    debounceTimer: null,

    page: 1,
    perPage: options.perPage || 20,
    hasMore: true,
    loadingMore: false,

    itemHeight: options.itemHeight || 40,
    overscan: options.overscan || 5,
    start: 0,
    end: 0,

    get totalHeight() {
      return this._filtered.length * this.itemHeight
    },

    get visibleItems() {
      return this._filtered.slice(this.start, this.end)
    },

    cache: new Map(),
    requestId: 0,
    lastRequestId: 0,

    getCacheKey(page = this.page) {
      return `${this.query}::${page}`
    },

    escapeHtml(str = '') {
      return str.replace(/[&<>"']/g, tag => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
      }[tag]))
    },

    dedupe(items) {
      const seen = new Set()
      return items.filter(i => {
        const key = i.value ?? i.label
        if (seen.has(key)) return false
        seen.add(key)
        return true
      })
    },

    abortAllRequests() {
      if (this.abortController) this.abortController.abort()
      if (this.prefetchController) this.prefetchController.abort()
      this.abortController = null
      this.prefetchController = null
    },

    init() {
      _popover.init.call(this)
      this.input = this.$root.querySelector('[data-tallkit-control]')
      this.items = this.$root.querySelector('[role=listbox]')

      this.setupARIA()
      this.bind()

      this.refreshItems()
      this.setupFuse()

      this.updateWindow()
      this.render()
      this.renderStates()

      this.$root.addEventListener('autocomplete-search', (e) => {
        this.onItemsUpdated(e.detail)
      })
    },

    refreshItems() {
      const nodes = Array.from(this.items.querySelectorAll('[role=option]'))

      this._items = nodes.map((el, index) => {
        const button = el.querySelector('button')
        const content = el.querySelector('[data-tallkit-button-content]')

        return {
          el,
          button,
          content,
          value: button?.value ?? content?.textContent?.trim(),
          label: content?.textContent?.trim(),
          index,
        }
      })

      this._itemsVersion++
    },

    setupFuse() {
      if (this.fuse) {
        this.fuse.setCollection(this._items)
      } else {
        this.fuse = new Fuse(this._items, {
          keys: ['label'],
          threshold: 0.4,
          ignoreLocation: true,
          ...this.fuseOptions,
        })
      }
    },

    triggerSearch() {
      clearTimeout(this.debounceTimer)

      this.debounceTimer = setTimeout(() => {
        this.resetSearchState()
        this.state = ((this.query ?? '').trim().length < this.minLength) ? 'idle' : 'loading'
        this.renderStates()
        this.updateARIA()

        if (this.state === 'idle') {
          return
        }

        this.fetch()
      }, this.delay)
    },

    fetch() {
      const key = this.getCacheKey()

      if (this.useCache && this.cache.has(key)) {
        this.onItemsUpdated(this.cache.get(key), true)
        return
      }

      if (this._items.length > 0) {
        this.search()
        return
      }

      this.abortAllRequests()
      this.abortController = new AbortController()

      const id = ++this.requestId
      this.lastRequestId = id

      this.$dispatch('autocomplete-search', {
        query: this.query,
        page: this.usePagination ? this.page : 1,
        perPage: this.perPage,
        requestId: id,
        signal: this.abortController.signal,
      })
    },

    prefetch() {
      if (!this.usePagination || !this.hasMore) return
      if (this.useCache && this.cache.has(this.getCacheKey(this.page + 1))) return

      if (this.prefetchController) this.prefetchController.abort()
      this.prefetchController = new AbortController()

      this.$dispatch('autocomplete-search', {
        query: this.query,
        page: this.page + 1,
        perPage: this.perPage,
        prefetch: true,
        signal: this.prefetchController.signal
      })
    },

    onItemsUpdated(payload, fromCache = false, backend = false) {
      if (!payload || !payload.items) {
        this.search(backend)
        return
      }

      const { items, hasMore = false, requestId } = payload
      if (!fromCache && requestId && requestId !== this.lastRequestId) return

      this.state = 'open'
      this.loadingMore = false

      const mapped = items.map((item, index) => ({
        ...item,
        index: this._items.length + index
      }))

      const merged = this.usePagination
        ? [...this._items, ...mapped]
        : mapped

      this._items = this.dedupe(merged)
      this._itemsVersion++
      this.hasMore = this.usePagination ? hasMore : false

      if (this.useCache) {
        this.cache.set(this.getCacheKey(), payload)
      }

      if (!backend) {
        this.setupFuse()
      }

      this.search(backend)

      if (this.usePagination) {
        this.prefetch()
      }
    },

    search(backend = false) {
      if (backend) {
        this._filtered = [...this._items]
      } else if (this.query !== this.lastQuery && this.fuse) {
        this._filtered = this.fuse.search(this.query).map(r => r.item)
      } else if (!this.query) {
        this._filtered = [...this._items]
      }

      this.lastQuery = this.query
      this.state = this._filtered.length ? 'open' : 'empty'
      this.open()

      this.updateWindow()
      this.render()
      this.renderStates()
      this.updateARIA()

      if (this._filtered.length && this.current == null) {
        this.setActive(0)
      }
    },

    highlight(item) {
      if (!item.content) return

      if (!this.highlightMatches || !this.query) {
        item.content.textContent = item.label
        return
      }

      let text = this.escapeHtml(item.label)

      const words = this.query.split(/\s+/).filter(Boolean)

      words.forEach(word => {
        const escaped = word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
        const regex = new RegExp(`(${escaped})`, 'gi')
        text = text.replace(regex, '<mark>$1</mark>')
      })

      item.content.innerHTML = text
    },

    renderStates() {
      if (this._renderStatesRAF) cancelAnimationFrame(this._renderStatesRAF)

      this._renderStatesRAF = requestAnimationFrame(() => {
        const toggle = (selector, show) => {
          this.$root.querySelectorAll(selector).forEach(el => {
            el.style.display = show ? '' : 'none'
          })
        }

        toggle('[data-tallkit-autocomplete-loading]', this.state === 'loading')
        toggle('[data-tallkit-autocomplete-empty]', this.state === 'empty')
        toggle('[data-tallkit-autocomplete-error]', this.state === 'error')
        toggle('[data-tallkit-autocomplete-loading-more]', this.loadingMore)

        this._renderStatesRAF = null
      })
    },

    calculateItemHeight() {
      if (!this._items.length) return

      const firstItem = this._items.find(i => i.el)
      if (!firstItem) return

      const rect = firstItem.el.getBoundingClientRect()
      this.itemHeight = rect.height || this.itemHeight
    },

    updateWindow() {
      if (!this.useVirtualization || !this.itemHeight) return
      if (!this.items) return

      const scrollTop = this.items.scrollTop
      const height = this.items.clientHeight

      const start = Math.floor(scrollTop / this.itemHeight)
      const visible = Math.ceil(height / this.itemHeight)

      this.start = Math.max(0, start - this.overscan)
      this.end = start + visible + this.overscan
    },

    render() {
      if (!this.itemHeight) this.calculateItemHeight()
      if (!this.items) return

      if (!this.useVirtualization) {
        this._filtered.forEach(item => {
          if (!item.el) return
          item.el.style.display = ''
          item.el.style.position = ''
          item.el.style.transform = ''
          this.highlight(item)
        })
        return
      }

      this.items.style.position = 'relative'
      this.items.style.height = `${this.totalHeight}px`

      this._items.forEach(item => {
        if (item.el) item.el.style.display = 'none'
      })

      this.visibleItems.forEach((item, i) => {
        if (!item.el) return

        const index = this.start + i

        item.el.style.display = ''
        item.el.style.position = 'absolute'
        item.el.style.left = '0'
        item.el.style.right = '0'
        item.el.style.transform = `translateY(${index * this.itemHeight}px)`

        this.highlight(item)
      })
    },

    bind() {
      let ticking = false

      bind(this.input, {
        ['@input']: (e) => {
          this.query = e.target.value
          this.triggerSearch()
        },

        ['@keydown.arrow-down.prevent']: () => {
          this.scrollBehavior = 'auto'
          this.next()
        },

        ['@keydown.arrow-up.prevent']: () => {
          this.scrollBehavior = 'auto'
          this.prev()
        },

        ['@keydown.home.prevent']: () => {
          this.scrollBehavior = 'auto'
          this.setActive(0)
        },

        ['@keydown.end.prevent']: () => {
          this.scrollBehavior = 'auto'
          this.setActive(this._filtered.length - 1)
        },

        ['@keydown.page-down.prevent']: () => {
          this.scrollBehavior = 'auto'
          this.pageDown()
        },

        ['@keydown.page-up.prevent']: () => {
          this.scrollBehavior = 'auto'
          this.pageUp()
        },

        ['@keydown.enter.prevent']: () => this.select(this.current),
      })

      if (this.items) {
        bind(this.items, {
          ['@click']: (e) => {
            const itemEl = e.target.closest('[data-tallkit-autocomplete-item-container]')
            if (!itemEl) return

            const index = this._items.findIndex(i => i.el === itemEl)
            if (index !== -1) this.select(index)
          },

          ['@mouseover']: (e) => {
            const itemEl = e.target.closest('[data-tallkit-autocomplete-item-container]')
            if (!itemEl) return

            const index = this._items.findIndex(i => i.el === itemEl)
            if (index !== -1) this.setActive(index)
          },

          ['@scroll']: () => {
            if (!ticking) {
              requestAnimationFrame(() => {
                this.updateWindow()
                this.render()

                if (this.usePagination) {
                  const nearBottom = this.items.scrollTop + this.items.clientHeight >= this.items.scrollHeight - 100
                  if (nearBottom) this.loadMore()
                }

                ticking = false
              })

              ticking = true
            }
          }
        })
      }
    },

    loadMore() {
      if (!this.usePagination) return
      if (!this.hasMore || this.loadingMore) return

      this.loadingMore = true
      this.renderStates()

      this.page++
      this.fetch()
    },

    next() {
      if (!this._filtered.length) return
      this.current = this.current == null ? 0 : (this.current + 1) % this._filtered.length
      this.ensureVisible()
      this.updateActive()
    },

    prev() {
      if (!this._filtered.length) return
      this.current = this.current == null ? 0 : (this.current - 1 + this._filtered.length) % this._filtered.length
      this.ensureVisible()
      this.updateActive()
    },

    pageDown() {
      if (!this._filtered.length) return
      if (!this.items) return

      const visibleCount = Math.floor(this.items.clientHeight / this.itemHeight)
      let nextIndex = (this.current ?? 0) + visibleCount
      if (nextIndex >= this._filtered.length) nextIndex = this._filtered.length - 1

      this.setActive(nextIndex)
    },

    pageUp() {
      if (!this._filtered.length) return
      if (!this.items) return

      const visibleCount = Math.floor(this.items.clientHeight / this.itemHeight)
      let prevIndex = (this.current ?? 0) - visibleCount
      if (prevIndex < 0) prevIndex = 0

      this.setActive(prevIndex)
    },

    setActive(index) {
      if (index < 0 || index >= this._filtered.length) return
      this.current = index
      this.ensureVisible()
      this.updateActive()
    },

    updateActive() {
      this._items.forEach(i => {
        i.button?.removeAttribute('data-active')
        i.el?.removeAttribute('id')
      })

      const item = this._filtered[this.current]
      if (!item) return

      const id = `ac-${this.uid}-item-${item.index}`
      item.el?.setAttribute('id', id)
      item.button?.setAttribute('data-active', '')

      this.input?.setAttribute('aria-activedescendant', id)
    },

    ensureVisible() {
      if (!this.items) return

      const top = this.current * this.itemHeight
      const bottom = top + this.itemHeight

      const isAbove = top < this.items.scrollTop
      const isBelow = bottom > this.items.scrollTop + this.items.clientHeight

      if (!isAbove && !isBelow) return

      this.items.scrollTo({
        top: isAbove ? top : bottom - this.items.clientHeight,
        behavior: this.scrollBehavior
      })
    },

    select(index) {
      if (index == null) return

      const item = this._filtered[index]
      if (!item) return

      this.scrollBehavior = 'smooth'

      this.selected = item.value
      this.query = item.label
      this.input.value = item.label

      this.$dispatch('input', item.value)
      this.$dispatch('autocomplete-selected', item)

      this.close()
    },

    resetSearchState() {
      this.abortAllRequests()
      this.current = null
      this.loadingMore = false
      this.state = 'idle'
      this._filtered = []
      this.page = 1
      this.hasMore = true
    },

    reset() {
      this.resetSearchState()
      this._items = []

      this.updateWindow()
      this.render()
      this.renderStates()
      this.updateARIA()
    },

    close() {
      this.abortAllRequests()

      this.state = 'idle'
      this.current = null

      this.updateActive()
      this.updateARIA()

      _popover.close.call(this)
    },

    setupARIA() {
      this.items?.setAttribute('id', `ac-${this.uid}-list`)
      this.items?.setAttribute('role', 'listbox')
      this.input?.setAttribute('role', 'combobox')
      this.input?.setAttribute('aria-autocomplete', 'list')
      this.input?.setAttribute('aria-expanded', 'false')
      this.input?.setAttribute('aria-controls', `ac-${this.uid}-list`)
      this.input?.setAttribute('aria-haspopup', 'listbox')
      this.input?.setAttribute('aria-live', 'polite')

    },

    updateARIA() {
      this.input?.setAttribute('aria-expanded', this.state === 'open' ? 'true' : 'false')
    }
  }
}

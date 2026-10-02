import { bind, setFieldChecked, allChecked, storageKey, getStoredPart, setStoredPart, removeStoredPart, isRtl, generateId, emit, clamp, prefersReducedMotion, keepAttributesOnMorph } from '../utils'

// Not exported: every function exported from a component file is registered as an Alpine component.
function columnVar(side, name) {
  return `--tk-column-${side}-${String(name).replace(/[^\w-]/g, '-')}`
}

export function table({
  draggable = false,
  resizable = false,
  toggleable = false,
  pinnable = false,
  persist = null,
  minColumnWidth = 80,
  maxMinColumnWidth = 400,
} = {}) {
  const tableKey = storageKey('table', persist)

  return {
    boundElements: new WeakSet(),
    rows: [],
    selected: [],
    selectedIds: [],
    selectAllChecked: false,
    observer: null,

    columnNames: [],
    columnOrder: [],
    columnVisibility: {},
    columnVisibilityDefault: {},
    columnShown: {},
    columnLocked: [],
    columnResizableNames: [],
    columnPinnable: [],
    columnPinned: {},
    columnPinnedDefault: {},
    columnFixedCount: 0,
    columnsDraggable: false,
    columnsResizable: false,
    columnOrderObserver: null,

    columnResizing: false,
    columnWidths: {},
    columnFixedWidths: {},
    columnMeasured: {},
    columnContentWidth: {},
    columnMinWidth: minColumnWidth,
    columnMaxMinWidth: maxMinColumnWidth || null,
    columnDefaultWidth: 160,
    columnMeasureObserver: null,
    columnMeasureFrame: null,

    columnStickyOffsets: {},
    columnStickyObserver: null,

    get columnAnchored() {
      return this.columnNames.filter((name) => this.columnLocked.includes(name) || this.columnPinned[name])
    },

    init() {
      this.resetSelection()

      const tbody = this.$root.querySelector('table > tbody')

      if (tbody) {
        this.observer = new MutationObserver(() => this.update())
        this.observer.observe(tbody, { childList: true, subtree: true })

        const inBody = (el, selector) => el.matches(selector) && el.closest('tbody') === tbody
        const stops = [
          keepAttributesOnMorph((el) => inBody(el, 'tr[role=row]'), ['data-state', 'data-expanded']),
          keepAttributesOnMorph((el) => inBody(el, 'button[data-role=row-expanded]'), ['aria-expanded', 'aria-controls']),
          keepAttributesOnMorph((el) => inBody(el, 'tr[data-role=row-expanded]'), ['id']),
        ]

        this._stopMorphHook = () => stops.forEach((stop) => stop())
      }

      this.columnsInit()
    },

    destroy() {
      this.observer?.disconnect()
      this._stopMorphHook?.()
      this.columnOrderObserver?.disconnect()
      this.columnMeasureObserver?.disconnect()
      this.columnStickyObserver?.disconnect()
      cancelAnimationFrame(this.columnMeasureFrame)
    },

    tableElement() {
      return this.$root.querySelector('table')
    },

    update() {
      const tbody = this.$root.querySelector('table > tbody')
      const trs = tbody ? Array.from(tbody.querySelectorAll(':scope > tr[role="row"]')) : []

      this.rows = trs.map(tr => {
        const selection = tr.querySelector('[data-role=row-selection]')
        const expanded = tr.querySelectorAll('[data-role=row-expanded]')

        const row = {
          el: tr,
          id: tr.dataset.id,
          selection,
          expanded,
        }

        if (selection && !this.boundElements.has(selection)) {
          this.boundElements.add(selection)

          bind(selection, {
            ['@click']() {
              this._updateRowState(row)
              this._syncSelect()
            }
          })
        }

        const unboundExpanded = Array.from(expanded).filter((el) => !this.boundElements.has(el))

        if (unboundExpanded.length) {
          unboundExpanded.forEach((el) => this.boundElements.add(el))

          bind(unboundExpanded, {
            ['@click']: () => {
              row.el.dataset.expanded = row.el.dataset.expanded === 'open' ? 'close' : 'open'
              this._updateRowState(row)
            }
          })
        }

        return row
      })

      this.rows.forEach((row) => {
        if (row.selection && row.id !== undefined) {
          setFieldChecked(row.selection, this.selectedIds.includes(row.id))
        }

        this._updateRowState(row)
      })

      this._syncSelect()
    },

    _pickableRows() {
      return this.rows.filter((row) => row.selection && !row.selection.disabled)
    },

    toggleAll() {
      this._pickableRows().forEach((row) => {
        setFieldChecked(row.selection, this.selectAllChecked)
        this._updateRowState(row)
      })
      this._syncSelect()
    },

    resetSelection() {
      this.selected = []
      this.selectedIds = []
      this.selectAllChecked = false
      this.update()
    },

    _updateRowState(row) {
      if (row.selection) {
        row.el.dataset.state = row.selection.checked ? 'checked' : 'unchecked'
      }

      if (row.expanded.length && !row.el.dataset.expanded) {
        row.el.dataset.expanded = 'close'
      }

      if (row.expanded.length) {
        const details = row.el.nextElementSibling?.matches('[data-role="row-expanded"]') ? row.el.nextElementSibling : null

        if (details && !details.id) details.id = generateId('table-row-details')

        row.expanded.forEach((el) => {
          el.setAttribute('aria-expanded', String(row.el.dataset.expanded === 'open'))
          if (details) el.setAttribute('aria-controls', details.id)
        })
      }
    },

    _syncSelect() {
      const shownIds = this.rows.filter((row) => row.selection && row.id !== undefined).map((row) => row.id)

      this.selected = this.rows.filter((row) => row.selection?.checked)
      this.selectedIds = [
        ...this.selectedIds.filter((id) => !shownIds.includes(id)),
        ...this.selected.map((row) => row.id).filter((id) => id !== undefined),
      ]
      this.selectAllChecked = allChecked(this._pickableRows(), (row) => row.selection.checked)
    },

    columnHeaderRow() {
      return this.$root.querySelector('table thead th[data-column-key]')?.parentElement ?? null
    },

    columnsInit() {
      const header = this.columnHeaderRow()

      if (header) this.columnsRead(header)

      this.columnsVisibilityInit()

      if (this.columnsDraggable) this.columnsOrderInit()
      if (this.columnsResizable) this.columnsWidthsInit()

      if (this.columnPinnable.length || Object.values(this.columnPinnedDefault).includes(true)) this.columnsPinInit()

      this.$nextTick(() => emit(this.tableElement(), 'ready'))
    },

    columnsRead(header) {
      const cells = Array.from(header.children).filter((th) => th.dataset.columnKey !== undefined)
      const names = (attribute) => cells.filter((th) => th.hasAttribute(attribute)).map((th) => th.dataset.columnKey)
      const draggableNames = names('data-column-draggable')

      this.columnNames = cells.map((th) => th.dataset.columnKey)
      this.columnOrder = [...this.columnNames]
      this.columnVisibilityDefault = Object.fromEntries(cells.map((th) => [th.dataset.columnKey, !th.hasAttribute('data-column-hidden')]))
      this.columnVisibility = { ...this.columnVisibilityDefault }
      this.columnLocked = this.columnNames.filter((name) => !draggableNames.includes(name))
      this.columnResizableNames = names('data-column-resizable')
      this.columnPinnable = names('data-column-pinnable')
      this.columnPinnedDefault = Object.fromEntries(cells.map((th) => [th.dataset.columnKey, th.dataset.columnSticky === 'left']))
      this.columnPinned = { ...this.columnPinnedDefault }
      this.columnFixedCount = header.children.length - cells.length
      this.columnsDraggable = draggable && draggableNames.length > 1
      this.columnsResizable = resizable && this.columnResizableNames.length > 0
    },

    isColumnHidden(name) {
      return this.columnVisibility[name] === false
    },

    columnHide(name) {
      this.columnVisibility[name] = false
    },

    columnsVisibilityInit() {
      if (toggleable) {
        const stored = getStoredPart(tableKey, 'visibility')

        if (stored && typeof stored === 'object' && !Array.isArray(stored)) {
          Object.keys(this.columnVisibility).forEach((name) => {
            if (typeof stored[name] === 'boolean') this.columnVisibility[name] = stored[name]
          })
        }
      }

      this.columnShown = { ...this.columnVisibility }

      this.$watch('columnVisibility', (value) => {
        if (toggleable) setStoredPart(tableKey, 'visibility', value)

        Object.keys(value).forEach((name) => {
          if (value[name] !== this.columnShown[name]) emit(this.tableElement(), 'column-toggled', { name, visible: value[name] })
        })

        this.columnShown = { ...value }

        if (this.columnsResizable) this.columnScheduleMeasure()
      })
    },

    columnReset() {
      if (toggleable) this.columnResetVisibility()
      if (this.columnsDraggable) this.columnResetOrder()
      if (this.columnsResizable) this.columnResetWidths()
      if (this.columnPinnable.length) this.columnResetPins()

      this.tableElement()?.parentElement?.scrollTo({ left: 0 })
    },

    columnResetVisibility() {
      this.columnVisibility = { ...this.columnVisibilityDefault }

      // The watcher stores whatever it is given: the entry is dropped afterwards.
      this.$nextTick(() => removeStoredPart(tableKey, 'visibility'))
    },

    columnsOrderInit() {
      const stored = getStoredPart(tableKey, 'order')

      if (Array.isArray(stored)) {
        const known = this.columnOrder
        const kept = [...new Set(stored)].filter((name) => known.includes(name))

        this.columnOrder = this.columnAnchor([...kept, ...known.filter((name) => !kept.includes(name))])
      }

      this.$watch('columnOrder', (value) => {
        setStoredPart(tableKey, 'order', value)
        this.applyColumnOrder()
        this.columnStickyRefresh()
      })

      this.applyColumnOrder()

      // Livewire morphs put the cells back in the server's order; not while x-sort drags ('sorting' on the body).
      const table = this.tableElement()

      if (table) {
        this.columnOrderObserver = new MutationObserver(() => {
          if (!document.body.classList.contains('sorting')) this.applyColumnOrder()
        })
        this.columnOrderObserver.observe(table, { childList: true, subtree: true })
      }
    },

    columnSortConfig() {
      return {
        onMove: (event) => event.related.hasAttribute('data-column-key') && !this.columnAnchored.includes(event.related.dataset.columnKey),
        // Replaces x-sort's own: its onEnd would move a morph marker and break Livewire's morphing.
        onStart: (event) => {
          document.body.classList.add('sorting')
          this.columnDragging(event.item.dataset.columnKey)
          this.columnMeasured = this.columnsResizable && this.columnResizeActive() ? this.columnMeasure().data : {}
        },
        onChange: (event) => {
          const order = this.columnResolveOrder(event.to)

          if (!order) return

          this.columnHold(event.to, order)
          this.applyColumnOrder(order, { skip: event.to, animate: true })
        },
        onEnd: () => {
          document.body.classList.remove('sorting')
          this.columnDragging(null)
        },
      }
    },

    columnDragging(key) {
      const table = this.tableElement()

      table?.querySelectorAll('[data-column-key]').forEach((cell) => {
        if (cell.matches('col') || cell.closest('table') !== table) return

        cell.toggleAttribute('data-column-dragging', key !== null && cell.dataset.columnKey === key)
      })
    },

    columnMovable(name) {
      return this.columnVisibility[name] && !this.columnAnchored.includes(name)
    },

    columnResolveOrder(row) {
      const movable = Array.from(row.children)
        .map((cell) => cell.dataset.columnKey)
        .filter((name) => name !== undefined && this.columnMovable(name))

      if (movable.length !== this.columnOrder.filter((name) => this.columnMovable(name)).length) return null

      return this.columnOrder.map((name) => this.columnMovable(name) ? movable.shift() : name)
    },

    columnHold(row, order) {
      order.forEach((name, index) => {
        if (this.columnMovable(name)) return

        const cells = Array.from(row.children).filter((cell) => cell.hasAttribute('data-column-key'))
        const cell = cells.find((candidate) => candidate.dataset.columnKey === name)
        const others = cells.filter((candidate) => candidate !== cell)

        if (!cell || cells.indexOf(cell) === index) return

        index < others.length ? others[index].before(cell) : others[others.length - 1].after(cell)
      })
    },

    columnSorted(row, key) {
      const cells = Array.from(row.children).filter((cell) => cell.hasAttribute('data-column-key'))
      const item = cells.find((cell) => cell.dataset.columnKey === key)
      const others = cells.filter((cell) => cell !== item)

      // Put back inside the block Livewire's morph markers wrap: x-sort can append it past them.
      if (item && others.length) {
        const index = cells.indexOf(item)

        index > 0 ? others[index - 1].after(item) : others[0].before(item)
      }

      const order = this.columnResolveOrder(row)

      if (!order) return

      this.columnOrder = order

      if (this.columnsResizable && this.columnResizeActive()) {
        const flexible = this.columnFlexible()

        this.columnNames.forEach((name) => {
          if (name !== flexible && this.columnResizableNames.includes(name) && this.columnVisibility[name] && this.columnWidths[name] === undefined) {
            this.columnWidths[name] = this.columnMeasured[name] ?? this.columnDefaultWidth
          }
        })
      }

      this.applyColumnOrder()
    },

    applyColumnOrder(order = this.columnOrder, { skip = null, animate = false } = {}) {
      const table = this.tableElement()

      if (!table) return

      const rank = (name) => order.indexOf(name)
      const rows = Array.from(table.querySelectorAll('tr, colgroup')).filter((row) => row !== skip && row.closest('table') === table)
      const dataCells = (row) => Array.from(row.children).filter((cell) => cell.hasAttribute('data-column-key'))

      const moving = animate && !prefersReducedMotion()
        ? rows.flatMap(dataCells).filter((cell) => !cell.hidden && !cell.matches('col'))
        : []
      const before = new Map(moving.map((cell) => [cell, cell.offsetLeft]))

      rows.forEach((row) => {
        const cells = dataCells(row)
        const sorted = [...cells].sort((a, b) => rank(a.dataset.columnKey) - rank(b.dataset.columnKey))

        if (sorted.every((cell, index) => cell === cells[index])) return

        const slots = cells.map((cell) => {
          const slot = document.createComment('')

          cell.replaceWith(slot)

          return slot
        })

        slots.forEach((slot, index) => slot.replaceWith(sorted[index]))
      })

      moving.forEach((cell) => {
        const distance = before.get(cell) - cell.offsetLeft

        if (distance) cell.animate({ transform: [`translateX(${distance}px)`, 'none'] }, { duration: 150, easing: 'ease' })
      })
    },

    columnAnchor(order) {
      const locked = this.columnNames.filter((name) => this.columnLocked.includes(name))
      const rest = order.filter((name) => !locked.includes(name))

      locked.forEach((name) => rest.splice(this.columnNames.indexOf(name), 0, name))

      return rest
    },

    columnResetOrder() {
      this.columnOrder = [...this.columnNames]

      // The watcher stores whatever it is given: the entry is dropped afterwards.
      this.$nextTick(() => removeStoredPart(tableKey, 'order'))
    },

    columnsWidthsInit() {
      const stored = getStoredPart(tableKey, 'widths')
      const widths = {}

      if (stored && typeof stored === 'object' && !Array.isArray(stored)) {
        this.columnResizableNames.forEach((name) => {
          if (typeof stored[name] === 'number' && Number.isFinite(stored[name])) widths[name] = clamp(Math.round(stored[name]), this.columnMinWidth, 10000)
        })
      }

      // Next tick: the children's bindings are only applied after init().
      if (Object.keys(widths).length) {
        this.$nextTick(() => {
          this.columnFixedWidths = this.columnMeasure().fixed
          this.columnContentWidth = this.columnMeasureContent()
          this.columnWidths = widths
        })
      }

      const table = this.tableElement()

      if (table) {
        this.columnMeasureObserver = new MutationObserver(() => this.columnScheduleMeasure())
        this.columnMeasureObserver.observe(table, { childList: true, subtree: true, characterData: true })
      }
    },

    columnResizeActive() {
      return Object.keys(this.columnWidths).length > 0
    },

    columnFlexible() {
      return [...this.columnOrder].reverse().find((name) => this.columnVisibility[name])
    },

    columnEffective(name, flexible = this.columnFlexible()) {
      if (!this.columnResizableNames.includes(name)) return this.columnFitFor(name)

      return Math.max(this.columnWidths[name] ?? (name === flexible ? 0 : this.columnDefaultWidth), this.columnMinFor(name))
    },

    columnStyle(name) {
      if (!this.columnResizeActive() || name === this.columnFlexible()) return ''

      return `width: ${this.columnEffective(name)}px`
    },

    columnFixedStyle(role) {
      return this.columnResizeActive() && this.columnFixedWidths[role] ? `width: ${this.columnFixedWidths[role]}px` : ''
    },

    columnResizeStyle() {
      if (!this.columnResizeActive()) return ''

      const flexible = this.columnFlexible()
      const data = this.columnNames
        .filter((name) => this.columnVisibility[name])
        .reduce((sum, name) => sum + this.columnEffective(name, flexible), 0)
      const fixed = Object.values(this.columnFixedWidths).reduce((sum, width) => sum + width, 0)

      return `table-layout: fixed; width: 100%; min-width: ${data + fixed}px`
    },

    columnTableStyle() {
      return [
        this.columnsResizable ? this.columnResizeStyle() : '',
        this.columnStickyStyle(),
      ].filter(Boolean).join('; ')
    },

    columnColspan() {
      return this.columnFixedCount + this.columnNames.filter((name) => this.columnVisibility[name]).length
    },

    columnMeasure() {
      const table = this.tableElement()
      const row = this.columnHeaderRow()
      const cols = table?.querySelector('colgroup')?.children
      const measured = { fixed: {}, data: {} }

      if (!row || !cols) return measured

      Array.from(row.children).forEach((th, index) => {
        const col = cols[index]

        if (!col || th.hidden) return

        const width = Math.round(th.getBoundingClientRect().width)

        if (col.dataset.columnFixed) measured.fixed[col.dataset.columnFixed] = width
        else measured.data[col.dataset.columnKey] = width
      })

      return measured
    },

    columnResizeFreeze() {
      const measured = this.columnMeasure()
      const flexible = this.columnFlexible()

      Object.entries(measured.data).forEach(([name, width]) => {
        if (name !== flexible && this.columnResizableNames.includes(name) && this.columnWidths[name] === undefined) this.columnWidths[name] = width
      })

      Object.entries(measured.fixed).forEach(([role, width]) => {
        if (this.columnFixedWidths[role] === undefined) this.columnFixedWidths[role] = width
      })
    },

    columnMeasureContent() {
      const table = this.tableElement()
      const row = this.columnHeaderRow()
      const cols = Array.from(table?.querySelector('colgroup')?.children ?? [])
      const min = {}

      if (!row || !cols.length) return min

      const aside = Array.from(table.querySelectorAll('tr')).filter((tr) => tr.closest('table') === table && tr !== row && !tr.hasAttribute('data-id'))
      const saved = { table: table.style.cssText, cols: cols.map((col) => col.style.cssText), rows: aside.map((tr) => tr.style.cssText) }

      table.style.cssText = 'table-layout: auto; width: auto; min-width: 0'
      cols.forEach((col) => col.style.cssText = '')
      aside.forEach((tr) => tr.style.display = 'none')

      Array.from(row.children).forEach((th, index) => {
        const name = cols[index]?.dataset.columnKey

        if (name && !th.hidden) min[name] = Math.ceil(th.getBoundingClientRect().width)
      })

      table.style.cssText = saved.table
      cols.forEach((col, index) => col.style.cssText = saved.cols[index])
      aside.forEach((tr, index) => tr.style.cssText = saved.rows[index])

      return min
    },

    columnMinFor(name) {
      return clamp(this.columnContentWidth[name] ?? 0, this.columnMinWidth, this.columnMaxMinWidth ?? Infinity)
    },

    columnFitFor(name) {
      return Math.max(this.columnMinWidth, this.columnContentWidth[name] ?? 0)
    },

    columnScheduleMeasure() {
      if (this.columnMeasureFrame) return

      this.columnMeasureFrame = requestAnimationFrame(() => {
        this.columnMeasureFrame = null

        if (!this.columnResizeActive() || this.columnResizing || document.body.classList.contains('sorting')) return

        const min = this.columnMeasureContent()

        if (JSON.stringify(min) !== JSON.stringify(this.columnContentWidth)) this.columnContentWidth = min
      })
    },

    columnResizeFit(name) {
      this.columnResizeFreeze()
      this.columnContentWidth = this.columnMeasureContent()
      this.columnWidths[name] = this.columnFitFor(name)
      this.columnSaveWidths()
    },

    columnResizeStart(name, event) {
      event.preventDefault()

      const handle = event.currentTarget
      const startX = event.clientX
      const startWidth = Math.round(handle.closest('th').getBoundingClientRect().width)
      const direction = isRtl(handle) ? -1 : 1
      let started = false
      let min = this.columnMinWidth

      handle.setPointerCapture(event.pointerId)

      const move = (moveEvent) => {
        if (!started && Math.abs(moveEvent.clientX - startX) >= 3) {
          started = true
          this.columnResizeFreeze()
          this.columnContentWidth = this.columnMeasureContent()
          min = this.columnMinFor(name)
          this.columnResizing = true
          document.body.style.cursor = 'col-resize'
        }

        if (!started) return

        this.columnWidths[name] = Math.max(min, Math.round(startWidth + direction * (moveEvent.clientX - startX)))
      }
      const stop = () => {
        handle.removeEventListener('pointermove', move)
        handle.removeEventListener('pointerup', stop)
        handle.removeEventListener('pointercancel', stop)
        document.body.style.cursor = ''
        this.columnResizing = false

        if (started) {
          this.columnSaveWidths()
          this.columnScheduleMeasure()
        }
      }

      handle.addEventListener('pointermove', move)
      handle.addEventListener('pointerup', stop)
      handle.addEventListener('pointercancel', stop)
    },

    columnResizeKey(name, event) {
      const handle = event.currentTarget
      const [narrower, wider] = isRtl(handle) ? ['ArrowRight', 'ArrowLeft'] : ['ArrowLeft', 'ArrowRight']

      if (event.key === 'Enter') {
        event.preventDefault()
        this.columnResizeFit(name)

        return
      }

      if (![narrower, wider, 'Home'].includes(event.key)) return

      event.preventDefault()

      this.columnResizeFreeze()
      this.columnContentWidth = this.columnMeasureContent()

      const min = this.columnMinFor(name)
      const width = Math.round(handle.closest('th').getBoundingClientRect().width)
      const step = (event.shiftKey ? 50 : 10) * (event.key === wider ? 1 : -1)

      this.columnWidths[name] = event.key === 'Home' ? min : Math.max(min, width + step)
      this.columnSaveWidths()
      this.columnScheduleMeasure()
    },

    columnResizeValue(name, handle) {
      void this.columnWidths[name]

      return Math.round(handle.closest('th')?.getBoundingClientRect().width ?? 0) || null
    },

    columnSaveWidths() {
      setStoredPart(tableKey, 'widths', this.columnWidths)
    },

    columnResetWidths() {
      this.columnWidths = {}
      this.columnFixedWidths = {}

      removeStoredPart(tableKey, 'widths')
    },

    isColumnPinned(name) {
      return this.columnPinned[name] === true
    },

    columnTogglePin(name) {
      if (!this.columnPinnable.includes(name)) return

      this.columnPinned[name] = !this.columnPinned[name]
    },

    columnsPinInit() {
      if (pinnable) {
        const stored = getStoredPart(tableKey, 'pinned')

        if (stored && typeof stored === 'object' && !Array.isArray(stored)) {
          this.columnPinnable.forEach((name) => {
            if (typeof stored[name] === 'boolean') this.columnPinned[name] = stored[name]
          })
        }
      }

      this.$watch('columnPinned', (value) => {
        if (pinnable) setStoredPart(tableKey, 'pinned', value)

        this.$nextTick(() => this.columnStickyRefresh())
      })

      this.$nextTick(() => this.columnStickyObserve())
    },

    columnResetPins() {
      this.columnPinned = { ...this.columnPinnedDefault }

      // The watcher stores whatever it is given: the entry is dropped afterwards.
      this.$nextTick(() => removeStoredPart(tableKey, 'pinned'))
    },

    columnStickyStyle() {
      return Object.entries(this.columnStickyOffsets).map(([name, left]) => `${columnVar('left', name)}: ${left}px`).join('; ')
    },

    columnStickyRefresh() {
      const row = this.columnHeaderRow()

      if (!row) return

      let left = 0
      const offsets = {}

      Array.from(row.children).forEach((th) => {
        const name = th.dataset.columnKey

        if (name === undefined || !this.columnPinned[name]) return

        offsets[name] = Math.round(left * 100) / 100

        if (!th.hidden) left += th.getBoundingClientRect().width
      })

      if (JSON.stringify(offsets) !== JSON.stringify(this.columnStickyOffsets)) this.columnStickyOffsets = offsets
    },

    columnStickyObserve() {
      const row = this.columnHeaderRow()

      this.columnStickyObserver?.disconnect()

      if (!row) return

      this.columnStickyObserver = new ResizeObserver(() => this.columnStickyRefresh())

      Array.from(row.children)
        .filter((th) => this.columnPinnable.includes(th.dataset.columnKey) || this.columnPinnedDefault[th.dataset.columnKey])
        .forEach((th) => this.columnStickyObserver.observe(th))

      this.columnStickyRefresh()
    },
  }
}

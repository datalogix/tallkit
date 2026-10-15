import { bind, dataKey, dataSelector, queryAllData, emit } from '../utils'
import { TRIGGER_SELECTOR } from './disclosure'

export function disclosureGroup({ exclusive = false } = {}) {
  return {
    observer: null,
    value: exclusive ? null : [],

    init() {
      const selector = dataSelector('disclosure-item')
      const own = (el) => el.matches?.(selector) && el.closest('[x-data^="disclosureGroup"]') === this.$root
      const getItems = () => queryAllData(this.$root, 'disclosure-item').filter(own)
      const itemsIn = (node) => node instanceof Element
        ? [node, ...queryAllData(node, 'disclosure-item')].filter((el) => el.matches(selector))
        : []
      const isOpen = (item) => item.hasAttribute('data-open')
      const normalize = (value) => [].concat(value ?? []).map(String)

      const settling = dataKey('disclosure-settling')

      const closedByGroup = new Set()

      const closeOthers = (keep) => getItems().forEach((item) => {
        if (item === keep || !isOpen(item)) return

        closedByGroup.add(item)
        item.removeAttribute('data-open')
      })

      const readValue = () => {
        const names = getItems().filter(isOpen).map((item) => item.dataset.name).filter((name) => name !== undefined)

        return exclusive ? (names[0] ?? null) : names
      }

      const syncValue = () => {
        const value = readValue()

        if (JSON.stringify(normalize(value)) === JSON.stringify(normalize(this.value))) return false

        this.value = value

        return true
      }

      const applyValue = () => {
        const names = normalize(this.value)

        getItems()
          .filter((item) => item.dataset.name !== undefined)
          .forEach((item) => item.toggleAttribute('data-open', names.includes(item.dataset.name)))
      }

      const triggers = () => getItems()
        .flatMap((item) => Array.from(item.querySelectorAll(TRIGGER_SELECTOR)))
        .filter((el) => !el.disabled && el.getAttribute('aria-disabled') !== 'true')

      const focusTrigger = (event, pick) => {
        const list = triggers()
        const index = list.indexOf(event.target)

        if (index === -1) return

        event.preventDefault()
        pick(list, index)?.focus()
      }

      if (exclusive) {
        const [first, ...rest] = getItems().filter(isOpen)

        if (rest.length) closeOthers(first)

        closedByGroup.clear()
      }

      this.value = readValue()

      this.observer = new MutationObserver((records) => {
        const changed = [...new Set(records.filter((record) => record.type === 'attributes').map((record) => record.target).filter(own))]
          .filter((item) => !closedByGroup.delete(item))
        const added = records.flatMap((record) => Array.from(record.addedNodes)).flatMap(itemsIn).filter(own)
        const removed = records.some((record) => Array.from(record.removedNodes).some((node) => itemsIn(node).length))

        if (!changed.length && !added.length && !removed) return

        if (exclusive) {
          const opened = [...changed, ...added].filter(isOpen).at(-1)

          if (opened) closeOthers(opened)
        }

        const dirty = syncValue() || changed.length

        if (dirty && !this.$root.hasAttribute(settling)) emit(this.$root, 'changed', { items: getItems() })
      })

      this.observer.observe(this.$root, { subtree: true, childList: true, attributeFilter: ['data-open'] })

      this.$watch('value', applyValue)

      // The model's initial value arrives after init: three frames cover Alpine's transition start, so it applies unanimated.
      this.$root.setAttribute(settling, '')
      requestAnimationFrame(() => requestAnimationFrame(() => requestAnimationFrame(() => this.$root.removeAttribute(settling))))

      bind(this.$root, {
        ['@keydown.arrow-down'](event) {
          focusTrigger(event, (list, index) => list[(index + 1) % list.length])
        },

        ['@keydown.arrow-up'](event) {
          focusTrigger(event, (list, index) => list.at(index - 1))
        },

        ['@keydown.home'](event) {
          focusTrigger(event, (list) => list[0])
        },

        ['@keydown.end'](event) {
          focusTrigger(event, (list) => list.at(-1))
        },
      })
    },

    destroy() {
      this.observer?.disconnect()
    }
  }
}

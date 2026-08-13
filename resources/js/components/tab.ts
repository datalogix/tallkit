import { bind } from '../utils'

export function tab({ selectFirst = null } = {}) {
  return {
    selected: null,

    get tabs() {
      return Array.from(this.$root.querySelectorAll('[role="tab"]')).filter(el => !el.disabled)
    },

    init() {
      const selected = this.$root.querySelector('[data-selected]')?.dataset.name

      if (selected || (selectFirst && this.tabs.length)) {
        this.$nextTick(() => {
          this.select(selected ?? this.tabs[0]?.dataset.name)
        })
      }

      bind(this.$root, {
        ['@keydown.arrow-right.prevent'](event) {
          this.focusTab(1, event.target)
        },

        ['@keydown.arrow-left.prevent'](event) {
          this.focusTab(-1, event.target)
        },

        ['@keydown.home.prevent'](event) {
          this.focusTab('first', event.target)
        },

        ['@keydown.end.prevent'](event) {
          this.focusTab('last', event.target)
        },
      })
    },

    isSelected(name) {
      return this.selected === name
    },

    select(name) {
      this.selected = name
    },

    focusTab(direction, current) {
      const tabs = this.tabs
      if (!tabs.length) return

      const currentIndex = tabs.indexOf(current)
      let index

      if (direction === 'first') index = 0
      else if (direction === 'last') index = tabs.length - 1
      else index = (currentIndex + direction + tabs.length) % tabs.length

      const next = tabs[index]
      next.focus()

      if (next.dataset.name) this.select(next.dataset.name)
    },
  }
}

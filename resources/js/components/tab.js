import { bind } from '../utils'

export function tab(
  {
    selectFirst = null,
    orientation = null
  } = {}
) {
  return {
    selected: null,

    tabs() {
      return (Array.from(this.$root.querySelectorAll('[role="tab"]'))).filter((el) => !el.disabled);
    },

    init() {
      const selected = this.$root.querySelector('[data-selected]')?.dataset.name
      const tabs = this.tabs()

      if (selected || (selectFirst && tabs.length)) {
        this.$nextTick(() => {
          this.select(selected ?? tabs[0]?.dataset.name)
        })
      }

      const nextKey = orientation === 'vertical' ? 'arrow-down' : 'arrow-right'
      const previousKey = orientation === 'vertical' ? 'arrow-up' : 'arrow-left'

      bind(this.$root, {
        [`@keydown.${nextKey}`](event) {
          if (!event.target.closest('[role="tab"]')) return
          event.preventDefault()
          this.focusTab(1, event.target)
        },

        [`@keydown.${previousKey}`](event) {
          if (!event.target.closest('[role="tab"]')) return
          event.preventDefault()
          this.focusTab(-1, event.target)
        },

        ['@keydown.home'](event) {
          if (!event.target.closest('[role="tab"]')) return
          event.preventDefault()
          this.focusTab('first', event.target)
        },

        ['@keydown.end'](event) {
          if (!event.target.closest('[role="tab"]')) return
          event.preventDefault()
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
      const tabs = this.tabs()
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
  };
}

import { dataSelector, bind, isRtl, emit } from '../utils'

export function tab(
  {
    selectFirst = null,
    orientation = null
  } = {}
) {
  return {
    selected: null,

    own(selector) {
      return Array.from(this.$root.querySelectorAll(selector)).filter((el) => el.closest(dataSelector('tab-group')) === this.$root)
    },

    isOwnTab(target) {
      const tab = target.closest('[role="tab"]')

      return !!tab && tab.closest(dataSelector('tab-group')) === this.$root
    },

    tabs() {
      return this.own('[role="tab"]')
        .filter((el) => !el.disabled && el.getAttribute('aria-disabled') !== 'true')
    },

    init() {
      const selected = this.own('[data-selected]')[0]?.dataset.name
      const tabs = this.tabs()

      if (selected || (selectFirst && tabs.length)) {
        this.$nextTick(() => {
          this.selected = selected ?? tabs[0]?.dataset.name
        })
      }

      const nextKey = orientation === 'vertical' ? 'arrow-down' : 'arrow-right'
      const previousKey = orientation === 'vertical' ? 'arrow-up' : 'arrow-left'

      const step = (forward) => orientation !== 'vertical' && isRtl(this.$root) ? -forward : forward

      bind(this.$root, {
        [`@keydown.${nextKey}`](event) {
          if (!this.isOwnTab(event.target)) return
          event.preventDefault()
          this.focusTab(step(1), event.target)
        },

        [`@keydown.${previousKey}`](event) {
          if (!this.isOwnTab(event.target)) return
          event.preventDefault()
          this.focusTab(step(-1), event.target)
        },

        ['@keydown.home'](event) {
          if (!this.isOwnTab(event.target)) return
          event.preventDefault()
          this.focusTab('first', event.target)
        },

        ['@keydown.end'](event) {
          if (!this.isOwnTab(event.target)) return
          event.preventDefault()
          this.focusTab('last', event.target)
        },
      })
    },

    isSelected(name) {
      return this.selected === name
    },

    select(name) {
      if (this.selected === name) return

      this.selected = name
      emit(this.$root, 'changed', { name })
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
  }
}

export function tab() {
  return {
    selected: null,

    init() {
      const selected = this.$root.querySelector('[data-selected]')?.dataset.name;

      if (selected) {
        this.$nextTick(() => {
          this.select(selected)
        })
      }
    },

    isSelected(name) {
      return this.selected === name
    },

    select(name) {
      this.selected = name
    },
  }
}

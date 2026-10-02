import { emit } from '../utils'

export function toggleable() {
  return {
    opened: false,

    init(opened = false) {
      this.opened = Boolean(opened)
    },

    open() {
      this.setOpened(true)
    },

    close() {
      this.setOpened(false)
    },

    setOpened(opened) {
      if (this.opened === opened) return

      this.opened = opened
      emit(this.$root, opened ? 'opened' : 'closed')
    },

    toggle(...args) {
      if (this.isOpened()) {
        this.close(...args)
      } else {
        this.open(...args)
      }
    },

    isOpened() {
      return this.opened === true
    },

    isClosed() {
      return this.opened === false
    }
  }
}

import { timeout } from "./timeout"

export function toggleable() {
  return {
    opened: false,
    lastOpened: null,

    init (opened = false) {
      if (Number.isInteger(opened)) {
        return timeout(() => this.open(), opened)
      }

      this.opened = Boolean(opened)
    },

    open (storage = true) {
      this.opened = true
      if (storage) this.lastOpened = this.opened
      //dispatch('open', this)
    },

    close (storage = true) {
      this.opened = false
      if (storage) this.lastOpened = this.opened
      //dispatch('close', this)
    },

    toggle (storage = true) {
      if (this.isOpened()) {
        this.close(storage)
      } else {
        this.open(storage)
      }
    },

    isOpened () {
      return this.opened === true
    },

    isClosed () {
      return this.opened === false
    }
  }
}

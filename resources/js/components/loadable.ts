import { timeout } from '../utils'

export function loadable () {
  return {
    empty: null,
    loaded: null,
    error: null,

    async load (cb: Function) {
      if (!this.$el.hasAttribute('data-silent')) {
        this.start()
      }

      try {
        const result = await cb()

        this.complete()

        if (result) {
          this.$nextTick(result)
        }
      } catch (e) {
        this.fail(e)
      }
    },

    reset () {
      this.empty = null
      this.loaded = null
      this.error = null
    },

    clear () {
      this.reset()
      this.empty = true
    },

    start () {
      this.reset()
      this.loaded = false
      this.$dispatch('started')
    },

    complete (milliseconds = 0) {
      timeout(() => {
        this.reset()
        this.loaded = true
        this.$dispatch('completed')
      }, milliseconds)
    },

    fail (error, milliseconds = 0) {
      timeout(() => {
        this.reset()
        this.error = error
        this.$dispatch('failed')
      }, milliseconds)
    },

    startAndComplete (completeOnNextTick = false) {
      this.start()

      if (completeOnNextTick) {
        this.$nextTick(() => this.complete())
      }
    },

    isEmpty () {
      return this.empty === true
    },

    isLoading () {
      return this.loaded === false
    },

    isCompleted () {
      return this.loaded === true
    },

    isError () {
      return this.error !== null
    }
  }
}

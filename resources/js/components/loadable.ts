export function loadable () {
  return {
    empty: null,
    loaded: null,
    error: null,

    _loadToken: 0,
    _pendingLoad: null as ReturnType<typeof setTimeout> | null,

    async load (cb: () => any, silent = false) {
      if (!silent && !this.$el.hasAttribute('data-silent')) {
        this.start()
      }

      const token = this._loadToken

      try {
        const result = await cb()

        this.complete(0, token)

        if (typeof result === 'function') {
          this.$nextTick(result)
        }
      } catch (e) {
        this.fail(e, 0, token)
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
      this._loadToken++
      this._clearPendingLoad()
      this.reset()
      this.loaded = false
      this.$dispatch('started')
    },

    complete (milliseconds = 0, token = this._loadToken) {
      this._clearPendingLoad()

      this._pendingLoad = setTimeout(() => {
        this._pendingLoad = null
        if (token !== this._loadToken) return

        this.reset()
        this.loaded = true
        this.$dispatch('completed')
      }, milliseconds)
    },

    fail (error, milliseconds = 0, token = this._loadToken) {
      this._clearPendingLoad()

      this._pendingLoad = setTimeout(() => {
        this._pendingLoad = null
        if (token !== this._loadToken) return

        this.reset()
        this.error = error
        this.$dispatch('failed')
      }, milliseconds)
    },

    _clearPendingLoad () {
      if (this._pendingLoad) {
        clearTimeout(this._pendingLoad)
        this._pendingLoad = null
      }
    },

    destroy () {
      this._clearPendingLoad()
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

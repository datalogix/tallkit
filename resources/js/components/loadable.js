import { emit, startTimeout } from '../utils'

export function loadable() {
  return {
    empty: null,
    loaded: null,
    error: null,

    // Only the latest load changes the state.
    _loadToken: 0,
    _pendingLoad: null,
    _destroyed: false,

    async load(cb, silent = false) {
      if (!silent && !this.$el.hasAttribute('data-silent')) {
        this.start()
      } else {
        this._loadToken++
      }

      const token = this._loadToken

      try {
        const result = await cb()

        this.complete(0, token)

        if (typeof result === 'function' && !this._destroyed) {
          this.$nextTick(result)
        }
      } catch (e) {
        if (e?.name === 'AbortError') return

        this.fail(e, 0, token)
      }
    },

    reset() {
      this.empty = null
      this.loaded = null
      this.error = null
    },

    clear() {
      this.reset()
      this.empty = true
    },

    start() {
      this._loadToken++
      this._clearPendingLoad()
      this.reset()
      this.loaded = false
      emit(this.$root, 'started')
    },

    complete(milliseconds = 0, token) {
      if (this._destroyed) return

      token ??= this._loadToken
      this._clearPendingLoad()

      this._pendingLoad = startTimeout(() => {
        this._pendingLoad = null
        if (token !== this._loadToken) return

        this.reset()
        this.loaded = true
        emit(this.$root, 'completed')
      }, milliseconds, 0)
    },

    fail(error, milliseconds = 0, token) {
      if (this._destroyed) return

      token ??= this._loadToken
      this._clearPendingLoad()

      this._pendingLoad = startTimeout(() => {
        this._pendingLoad = null
        if (token !== this._loadToken) return

        this.reset()
        this.error = error
        emit(this.$root, 'failed')
      }, milliseconds, 0)
    },

    _clearPendingLoad() {
      if (this._pendingLoad) {
        clearTimeout(this._pendingLoad)
        this._pendingLoad = null
      }
    },

    destroy() {
      this._destroyed = true
      this._clearPendingLoad()
    },

    isDestroyed() {
      return this._destroyed
    },

    startAndComplete(completeOnNextTick = false) {
      this.start()

      if (completeOnNextTick) {
        this.$nextTick(() => this.complete())
      }
    },

    isEmpty() {
      return this.empty === true
    },

    isLoading() {
      return this.loaded === false
    },

    isCompleted() {
      return this.loaded === true
    },

    isError() {
      return this.error !== null
    }
  }
}

import { bind, hasLivewire, onLivewireCommit, startTimeout, toMilliseconds } from '../utils'

export function toggle({ action = null, model = null, delay = null, minDuration = null } = {}) {
  return {
    blocking: false,
    busy: false,
    busyShownAt: null,
    delayTimeout: null,
    minDurationTimeout: null,
    originalDisabled: false,
    livewireCommitCleanup: null,

    init() {
      const input = this.$root.querySelector('input[type="checkbox"]')
      this.originalDisabled = !!input?.disabled

      if (hasLivewire()) {
        this.watchLivewireCommits()
      }

      bind(input, {
        [':aria-busy']: () => (this.blocking ? 'true' : null),
        [':aria-disabled']: () => (this.blocking || this.originalDisabled ? 'true' : null),
        ['@click'](event) {
          if (this.blocking) event.preventDefault()
        },
      })
    },

    watchLivewireCommits() {
      this.livewireCommitCleanup = onLivewireCommit(({ component, commit, succeed, fail }) => {
        if (component?.el !== this.$el && !component?.el?.contains(this.$el)) return

        const matchesAction = action && commit?.calls?.some((call) => call.method === action)
        const matchesModel = model && Object.prototype.hasOwnProperty.call(commit?.updates ?? {}, model)

        if (!matchesAction && !matchesModel) return

        clearTimeout(this.delayTimeout)
        clearTimeout(this.minDurationTimeout)

        this.blocking = true

        this.delayTimeout = startTimeout(() => {
          this.busy = true
          this.busyShownAt = Date.now()
        }, delay, 150)

        const stop = () => {
          this.blocking = false
          clearTimeout(this.delayTimeout)

          if (!this.busy) return

          const remaining = toMilliseconds(minDuration, 700) - (Date.now() - this.busyShownAt)

          if (remaining > 0) {
            this.minDurationTimeout = startTimeout(() => { this.busy = false }, remaining)
          } else {
            this.busy = false
          }
        }

        succeed(stop)
        fail(stop)
      })
    },

    destroy() {
      clearTimeout(this.delayTimeout)
      clearTimeout(this.minDurationTimeout)
      this.livewireCommitCleanup?.()
    },
  }
}

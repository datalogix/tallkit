export function form ({
  focusError = null,
  toast = null,
  errorMessage = null,
  successMessage = null
} = {}) {
  return {
    livewireCommitCleanup: null,

    init() {
      if (window.Livewire) {
        this.watchLivewireCommits()
      } else if (focusError) {
        this.focusFirstInvalidField()
      }
    },

    watchLivewireCommits() {
      const offCommit = window.Livewire.hook('commit', ({ component, succeed }) => {
        succeed(({ snapshot }) => {
          if (!this.$el?.isConnected) return
          if (component?.el !== this.$el && !component?.el?.contains(this.$el)) return

          const id = this.$el?.getAttribute('id')
            ?? component?.el.getAttribute('wire:id')
            ?? undefined

          const hasErrors = Object.keys(snapshot?.memo?.errors ?? {}).length > 0
            || !!this.$el.querySelector('[data-invalid], [aria-invalid="true"]')

          if (hasErrors) {
            if ((toast === true || toast === 'error') && errorMessage) {
              this.$tallkit.toast().error({ message: errorMessage, id, duration: 3000 })
            }

            if (focusError) {
              this.focusFirstInvalidField()
            }

            return
          }

          if ((toast === true || toast === 'success') && successMessage) {
            this.$tallkit.toast().success({ message: successMessage, id, duration: 3000 })

            return
          }
        })
      })

      this.livewireCommitCleanup = typeof offCommit === 'function'
        ? offCommit
        : () => {}
    },

    focusFirstInvalidField() {
      const field = this.$el.querySelector('[data-invalid], [aria-invalid="true"]')

      if (!(field instanceof HTMLElement)) return

      field.scrollIntoView({ behavior: 'smooth', block: 'center' })
      field.focus({ preventScroll: true })
    },

    destroy() {
      this.livewireCommitCleanup?.()
    }
  }
}

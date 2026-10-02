import { dataSelector, hasLivewire, onLivewireCommit, prefersReducedMotion } from '../utils'

export function form(
  {
    action = null,
    focusError = null,
    clearErrorsOnSubmit = null,
    toast = null,
    errorMessage = null,
    successMessage = null
  } = {}
) {
  return {
    livewireCommitCleanup: null,

    init() {
      if (hasLivewire() && this.$el.closest('[wire\\:id]')) {
        this.watchLivewireCommits()
      } else if (focusError) {
        this.focusFirstInvalidField()
      }
    },

    watchLivewireCommits() {
      this.livewireCommitCleanup = onLivewireCommit(({ component, commit, succeed }) => {
        if (component?.el !== this.$el && !component?.el?.contains(this.$el)) return

        const calls = commit?.calls ?? []
        const method = action ?? this.submitMethod()

        if (method ? !calls.some((call) => call.method === method) : calls.length === 0) return

        if (clearErrorsOnSubmit) {
          this.clearErrors()
        }

        succeed(({
          snapshot
        }) => {
          if (!this.$el?.isConnected) return

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
    },

    submitMethod() {
      const form = this.$el.matches('form') ? this.$el : this.$el.querySelector('form')
      const attribute = Array.from(form?.attributes ?? []).find((attr) => attr.name.startsWith('wire:submit'))
      const method = attribute?.value.trim().split('(')[0].trim()

      return method || null
    },

    clearErrors() {
      this.$el.querySelectorAll('[data-invalid], [aria-invalid="true"]').forEach((field) => {
        field.removeAttribute('data-invalid')
        field.removeAttribute('aria-invalid')
      })

      this.$el.querySelectorAll(`${dataSelector('error')}, ${dataSelector('error-group')}`).forEach((el) => el.remove())
    },

    focusFirstInvalidField() {
      const focusable = 'input:not([type=hidden]):not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [contenteditable="true"], [tabindex]:not([tabindex="-1"])'
      const field = Array.from(this.$el.querySelectorAll('[data-invalid], [aria-invalid="true"]'))
        .map((marked) => marked.matches(focusable)
          ? marked
          : marked.querySelector(focusable) ?? marked.closest(`${dataSelector('field-control')}, ${dataSelector('field')}`)?.querySelector(focusable))
        .find(Boolean)

      if (!(field instanceof HTMLElement)) return

      field.scrollIntoView({ behavior: prefersReducedMotion() ? 'auto' : 'smooth', block: 'center' })
      field.focus({ preventScroll: true })
    },

    destroy() {
      this.livewireCommitCleanup?.()
    }
  }
}

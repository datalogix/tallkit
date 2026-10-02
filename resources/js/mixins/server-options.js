import { dataSelector, onLivewireCommit } from '../utils'

// Outside the wire:ignore around the chart, so each Livewire update brings them.
export function serverOptions() {
  return {
    _serverOptionsText: null,
    _stopServerOptions: null,

    serverOptionsElement() {
      const el = this.$root?.nextElementSibling

      return el?.matches?.(`script${dataSelector('options')}`) ? el : null
    },

    serverOptions() {
      const el = this.serverOptionsElement()

      if (!el) return null

      this._serverOptionsText = el.textContent

      try {
        return JSON.parse(el.textContent)
      } catch {
        return null
      }
    },

    followServerOptions(apply) {
      this._serverOptionsText ??= this.serverOptionsElement()?.textContent ?? null

      this._stopServerOptions = onLivewireCommit(({ component, succeed }) => {
        if (component?.el && !component.el.contains(this.$root)) return

        succeed(() => this.$nextTick(() => {
          const el = this.serverOptionsElement()

          if (!el || el.textContent === this._serverOptionsText) return

          const next = this.serverOptions()

          if (next) apply(next)
        }))
      })
    },

    stopFollowingServerOptions() {
      this._stopServerOptions?.()
      this._stopServerOptions = null
    },
  }
}

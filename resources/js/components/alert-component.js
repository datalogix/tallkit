import { dataKey, timeout as _timeout, bind } from '../utils'
import { dismissible } from '../mixins/dismissible'

export function alertComponent({ timeout = 0, pauseOnHover = false } = {}) {
  const _dismissible = dismissible('collapse')

  return {
    ..._dismissible,

    timeoutId: null,

    remaining: timeout,
    startedAt: 0,

    pauseReasons: new Set(),

    progressEl: null,
    visibilityHandler: null,

    state: 'idle',

    init() {
      _dismissible.init.call(this)
      this.progressEl = this.$root.querySelector(dataKey('alert-progress'))

      this.startTimer()
      this.initProgress()

      this.visibilityHandler = this.handleVisibility.bind(this)
      document.addEventListener('visibilitychange', this.visibilityHandler)

      bind(this.$root, {
        ...(pauseOnHover ? {
          ['@mouseenter']: () => this.pause('hover'),
          ['@mouseleave']: () => this.resume('hover'),
        } : {}),

        ['@pause']: () => this.pause('external'),
        ['@resume']: () => this.resume('external'),
      })
    },

    startTimer() {
      if (!timeout || this.remaining <= 0) return

      this.state = 'running'
      this.startedAt = Date.now()

      this.timeoutId = _timeout(
        () => this.dismiss('timeout'),
        this.remaining,
        7000
      )
    },

    pause(reason = 'manual') {
      this.pauseReasons.add(reason)

      if (this.pauseReasons.size > 1) return
      if (!this.timeoutId) return

      const elapsed = Date.now() - this.startedAt
      this.remaining = Math.max(this.remaining - elapsed, 0)

      clearTimeout(this.timeoutId)
      this.timeoutId = null

      this.state = 'paused'

      this.freezeProgress()
    },

    resume(reason = 'manual') {
      this.pauseReasons.delete(reason)

      if (this.pauseReasons.size > 0) return
      if (this.remaining <= 0) return

      this.state = 'running'

      if (this.progressEl) {
        this.progressEl.style.transitionDuration = '150ms'

        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            if (!this.progressEl) return

            this.progressEl.style.transitionTimingFunction = 'linear'
            this.progressEl.style.transitionDuration = `${this.remaining}ms`

            this.applyProgress(0)
          })
        })
      }

      this.startTimer()
    },

    handleVisibility() {
      if (document.hidden) {
        this.pause('visibility')
      } else {
        this.resume('visibility')
      }
    },

    initProgress() {
      if (!this.progressEl || !this.remaining) return

      this.progressEl.style.transitionTimingFunction = 'linear'
      this.applyProgress(100)

      requestAnimationFrame(() => {
        if (!this.progressEl) return

        this.progressEl.style.transitionDuration = `${this.remaining}ms`
        this.applyProgress(0)
      })
    },

    applyProgress(percent) {
      if (!this.progressEl) return

      this.progressEl.style.backgroundSize = `${percent}% 100%`
    },

    freezeProgress() {
      if (!this.progressEl) return

      const computed = getComputedStyle(this.progressEl)
      const size = computed.backgroundSize

      this.progressEl.style.transitionDuration = '0ms'
      this.progressEl.style.backgroundSize = size
    },

    beforeDismiss() {
      this.state = 'dismissing'

      if (this.timeoutId) {
        clearTimeout(this.timeoutId)
        this.timeoutId = null
      }
    },

    destroy() {
      if (this.timeoutId) {
        clearTimeout(this.timeoutId)
        this.timeoutId = null
      }

      if (this.visibilityHandler) {
        document.removeEventListener('visibilitychange', this.visibilityHandler)
        this.visibilityHandler = null
      }

      _dismissible.destroy.call(this)

      this.pauseReasons.clear()
      this.state = 'idle'
    },
  };
}

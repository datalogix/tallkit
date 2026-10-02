import { queryData, eventName, startTimeout, toMilliseconds, bind } from '../utils'
import { dismissible } from '../mixins/dismissible'

export function alertComponent({ duration: given = 0, pauseOnHover = false } = {}) {
  const _dismissible = dismissible('collapse')
  const duration = given === true ? 7000 : toMilliseconds(given)

  return {
    ..._dismissible,

    timeoutId: null,

    remaining: duration,
    startedAt: 0,

    pauseReasons: new Set(),

    progressEl: null,
    visibilityHandler: null,

    state: 'idle',

    init() {
      _dismissible.init.call(this)
      this.progressEl = queryData(this.$root, 'alert-progress')

      this.startTimer()
      this.initProgress()

      this.visibilityHandler = this.handleVisibility.bind(this)
      document.addEventListener('visibilitychange', this.visibilityHandler)

      if (document.hidden) this.pause('visibility')

      bind(this.$root, {
        ...(pauseOnHover ? {
          ['@mouseenter']: () => this.pause('hover'),
          ['@mouseleave']: () => this.resume('hover'),
        } : {}),

        ['@focusin']: () => this.pause('focus'),
        ['@focusout']: (event) => {
          if (!this.$root.contains(event.relatedTarget)) this.resume('focus')
        },

        [`@${eventName('pause')}`]: () => this.pause('external'),
        [`@${eventName('resume')}`]: () => this.resume('external'),

        ['@restored.self']: () => this.$nextTick(() => this.restart()),
      })
    },

    startTimer() {
      if (!duration || this.remaining <= 0 || this.timeoutId || this.state === 'dismissing') return

      this.state = 'running'
      this.startedAt = Date.now()

      this.timeoutId = startTimeout(
        () => this.dismiss('timeout'),
        this.remaining,
        duration
      )
    },

    pause(reason = 'manual') {
      this.pauseReasons.add(reason)

      if (!this.timeoutId) return

      const elapsed = Date.now() - this.startedAt
      this.remaining = Math.max(this.remaining - elapsed, 0)

      clearTimeout(this.timeoutId)
      this.timeoutId = null

      this.state = 'paused'

      this.freezeProgress()
    },

    resume(reason = 'manual') {
      if (!this.pauseReasons.delete(reason)) return

      if (this.pauseReasons.size > 0) return
      if (this.state !== 'paused' || this.remaining <= 0) return

      if (this.progressEl) {
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            if (!this.progressEl || this.state !== 'running') return

            this.progressEl.style.transitionTimingFunction = 'linear'
            this.progressEl.style.transitionDuration = `${this.remaining}ms`

            this.applyProgress(0)
          })
        })
      }

      this.startTimer()
    },

    restart() {
      if (this.timeoutId) clearTimeout(this.timeoutId)

      this.timeoutId = null
      this.remaining = duration
      this.state = 'idle'
      this.pauseReasons.clear()

      this.progressEl = queryData(this.$root, 'alert-progress')

      if (this.progressEl) this.progressEl.style.transitionDuration = '0ms'

      this.startTimer()
      this.initProgress()

      if (document.hidden) this.pause('visibility')
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
        if (!this.progressEl || this.state !== 'running') return

        void this.progressEl.offsetWidth

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

      const size = getComputedStyle(this.progressEl).backgroundSize

      this.progressEl.style.transitionDuration = '0ms'
      this.progressEl.style.backgroundSize = size
    },

    beforeDismiss() {
      this.state = 'dismissing'
      this.remaining = 0

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
  }
}

import { eventName, startTimeout, toMilliseconds, bind } from '../utils'
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

    progressValue: 100,
    progressFrame: null,
    visibilityHandler: null,

    state: 'idle',

    init() {
      _dismissible.init.call(this)

      this.startTimer()

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

      this.trackProgress()
    },

    trackProgress() {
      cancelAnimationFrame(this.progressFrame)

      const step = () => {
        if (this.state !== 'running') return

        const left = this.remaining - (Date.now() - this.startedAt)

        this.progressValue = Math.max(0, Math.min(100, (left / duration) * 100))
        this.progressFrame = requestAnimationFrame(step)
      }

      step()
    },

    pause(reason = 'manual') {
      this.pauseReasons.add(reason)

      if (!this.timeoutId) return

      const elapsed = Date.now() - this.startedAt
      this.remaining = Math.max(this.remaining - elapsed, 0)

      clearTimeout(this.timeoutId)
      this.timeoutId = null

      this.state = 'paused'

      cancelAnimationFrame(this.progressFrame)
      this.progressValue = (this.remaining / duration) * 100
    },

    resume(reason = 'manual') {
      if (!this.pauseReasons.delete(reason)) return

      if (this.pauseReasons.size > 0) return
      if (this.state !== 'paused' || this.remaining <= 0) return

      this.startTimer()
    },

    restart() {
      if (this.timeoutId) clearTimeout(this.timeoutId)

      this.timeoutId = null
      this.remaining = duration
      this.state = 'idle'
      this.pauseReasons.clear()
      this.progressValue = 100

      this.startTimer()

      if (document.hidden) this.pause('visibility')
    },

    handleVisibility() {
      if (document.hidden) {
        this.pause('visibility')
      } else {
        this.resume('visibility')
      }
    },

    beforeDismiss() {
      this.state = 'dismissing'
      this.remaining = 0

      cancelAnimationFrame(this.progressFrame)

      if (this.timeoutId) {
        clearTimeout(this.timeoutId)
        this.timeoutId = null
      }
    },

    destroy() {
      cancelAnimationFrame(this.progressFrame)

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

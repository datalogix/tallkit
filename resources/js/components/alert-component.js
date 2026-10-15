import { eventName, startTimeout, toMilliseconds, readingDuration, bind, queryData } from '../utils'
import { dismissible } from '../mixins/dismissible'

export function alertComponent({ duration: given = 0, pauseOnHover = false } = {}) {
  const _dismissible = dismissible('collapse')
  let duration = 0

  return {
    ..._dismissible,

    timeoutId: null,

    remaining: 0,
    startedAt: 0,

    pauseReasons: new Set(),

    progressValue: 100,
    progressAnimation: null,
    visibilityHandler: null,

    state: 'idle',

    init() {
      _dismissible.init.call(this)

      duration = given === true
        ? readingDuration('', this.$root.textContent.replace(/\s+/g, ' ').trim())
        : toMilliseconds(given)

      this.remaining = duration

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
      const bar = queryData(queryData(this.$root, 'alert-progress'), 'progress-bar')

      if (!bar?.animate) return

      if (this.progressAnimation?.playState === 'paused') {
        this.progressAnimation.play()
        return
      }

      this.stopProgress()

      this.progressAnimation = bar.animate(
        [{ width: `${(this.remaining / duration) * 100}%` }, { width: '0%' }],
        { duration: this.remaining, easing: 'linear', fill: 'forwards' }
      )
    },

    stopProgress() {
      this.progressAnimation?.cancel()
      this.progressAnimation = null
    },

    pause(reason = 'manual') {
      this.pauseReasons.add(reason)

      if (!this.timeoutId) return

      const elapsed = Date.now() - this.startedAt
      this.remaining = Math.max(this.remaining - elapsed, 0)

      clearTimeout(this.timeoutId)
      this.timeoutId = null

      this.state = 'paused'

      this.progressAnimation?.pause()
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
      this.stopProgress()

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

      this.progressAnimation?.pause()

      if (this.timeoutId) {
        clearTimeout(this.timeoutId)
        this.timeoutId = null
      }
    },

    destroy() {
      this.stopProgress()

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

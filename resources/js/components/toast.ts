import { bind } from '../utils'

export function toast() {
  return {
    toasts: [],
    positions: ['top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right'],

    init() {
      bind(this.$el, {
        ['@toast.document'](e) {
          this.addToast(e.detail)
        }
      })

      this.initAttentionListeners()
    },

    initAttentionListeners() {
      this.isPageVisible = true
      this.isUserActive = true
      this.idleTimeout = null
      this.idleDelay = 10000

      this._listeners = []

      let ticking = false

      const markActive = () => {
        if (ticking) return
        ticking = true

        requestAnimationFrame(() => {
          this.isUserActive = true
          this.resetIdleTimer()
          this.syncAttention()
          ticking = false
        })
      }

      const markIdle = () => {
        this.isUserActive = false
        this.syncAttention()
      }

      const add = (target, event, handler, options) => {
        target.addEventListener(event, handler, options)
        this._listeners.push(() => target.removeEventListener(event, handler, options))
      }

      add(document, 'visibilitychange', () => {
        this.isPageVisible = !document.hidden
        this.syncAttention()
      })

      ;['mousemove', 'mousedown', 'keydown', 'touchstart'].forEach(event => {
        add(window, event, markActive, { passive: true })
      })

      this.resetIdleTimer = () => {
        clearTimeout(this.idleTimeout)
        this.idleTimeout = setTimeout(markIdle, this.idleDelay)
      }

      this.resetIdleTimer()

      this.$el.addEventListener('alpine:destroy', () => {
        this._listeners.forEach(off => off())
      })
    },

    syncAttention() {
      const shouldRun = this.isPageVisible && this.isUserActive

      this.toasts.forEach(toast => {
        if (!toast.duration || !toast.attentionAware) return

        if (shouldRun && toast.pausedAt) {
          toast.resume()
        }

        if (!shouldRun && !toast.pausedAt) {
          toast.pause()
        }
      })
    },

    addToast(props) {
      /*
      if (this.toasts.length >= 5) {
        const oldest = this.toasts.slice().sort((a, b) => a.createdAt - b.createdAt)[0]

        if (oldest) {
          this.removeToast(oldest.id)
        }
      }
      */

      const duration = props.duration ?? getDynamicDuration(props.title, props.message)

      const toast = window.Alpine.reactive({
        id: crypto.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`,
        createdAt: Date.now(),
        ...props,
        duration,
        position: normalizePosition(props.position),

        attentionAware: props.attentionAware ?? true,
        progress: props.progress ?? true,
        pauseOnHover: props.pauseOnHover ?? true,
        swipe: props.swipe ?? true,

        visible: false,

        progressValue: 1,
        startTime: 0,
        total: duration,
        elapsedBeforePause: 0,
        raf: null,
        pausedAt: null,

        swiping: false,
        startX: 0,
        startY: 0,
        currentX: 0,
        currentY: 0,
        lockDirection: null,

        start() {
          if (!this.duration) return

          this.startTime = performance.now()

          const loop = (time) => {
            if (!this.$root.toasts.find(t => t.id === this.id)) {
              this.stop()
              return
            }

            if (this.pausedAt) return

            const elapsed = this.elapsedBeforePause + (time - this.startTime)
            const linear = Math.min(elapsed / this.total, 1)
            const eased = linear === 1 ? 1 : 1 - Math.pow(2, -10 * linear)

            if (this.progress) {
              this.progressValue = 1 - eased
            }

            if (linear >= 1) {
              this.$root.removeToast(this.id)
              return
            }

            this.raf = requestAnimationFrame(loop)
          }

          this.raf = requestAnimationFrame(loop)
        },

        pause() {
          if (!this.duration || this.pausedAt) return

          this.pausedAt = performance.now()
          this.elapsedBeforePause += this.pausedAt - this.startTime

          if (this.raf) {
            cancelAnimationFrame(this.raf)
            this.raf = null
          }
        },

        resume() {
          if (!this.pausedAt) return

          this.pausedAt = null
          this.start()
        },

        stop() {
          if (this.raf) {
            cancelAnimationFrame(this.raf)
            this.raf = null
          }
        },

        onPointerDown(e) {
          if (!this.swipe) return

          this.swiping = true
          this.startX = e.clientX
          this.startY = e.clientY
          this.lockDirection = null
        },

        onPointerMove(e) {
          if (!this.swipe || !this.swiping) return

          this.currentX = e.clientX - this.startX
          this.currentY = e.clientY - this.startY

          if (!this.lockDirection) {
            this.lockDirection = Math.abs(this.currentX) > Math.abs(this.currentY) ? 'x' : 'y'
          }

          if (this.lockDirection === 'x') {
            e.preventDefault()
          }
        },

        onPointerUp(e) {
          if (!this.swipe) return

          this.swiping = false

          if (this.lockDirection !== 'x') {
            this.currentX = 0
            this.currentY = 0
            this.lockDirection = null
            return
          }

          const width = e.currentTarget.offsetWidth
          const threshold = width * 0.4

          if (Math.abs(this.currentX) > threshold) {
            this.$root.removeToast(this.id)
          } else {
            this.currentX = 0
            this.currentY = 0
            this.lockDirection = null
          }
        }
      })

      this.toasts.push(toast)

      this.$nextTick(() => {
        toast.visible = true
        toast.start()
      })

      return toast
    },

    updateToast(id, data) {
      const toast = this.toasts.find(t => t.id === id)
      if (!toast) return


      const allowed = [
        'title',
        'message',
        'type',
        'size',
        'duration',
        'position',
        'attentionAware',
        'progress',
        'pauseOnHover',
        'swipe',
      ];

      for (const key in data) {
        if (allowed.includes(key)) {
          toast[key] = data[key]
        }
      }

      toast.currentX = 0
      toast.swiping = false

      if (data.duration !== undefined) {
        toast.stop()

        toast.pausedAt = null
        toast.total = data.duration
        toast.elapsedBeforePause = 0

        toast.progressValue = 1

        if (toast.visible) {
          toast.start()
        }
      }
    },

    removeToast(id) {
      const toast = this.toasts.find(t => t.id === id)
      if (!toast) return

      toast.stop()
      toast.raf = null
      toast.visible = false

      setTimeout(() => {
        this.toasts = this.toasts.filter(t => t.id !== id)
      }, 300)
    },

    getToastsByPosition(position) {
      return this.toasts.filter(t => t.position === position)
    },

    notify(props) {
      return this.addToast(props)
    },

    success(message, props = {}) {
      return this.notify({
        title: message,
        type: 'success',
        ...props
      })
    },

    error(message, props = {}) {
      return this.notify({
        title: message,
        type: 'danger',
        duration: 7000,
        ...props
      })
    },

    info(message, props = {}) {
      return this.notify({
        title: message,
        type: 'info',
        ...props
      })
    },

    loading(message, props = {}) {
      return this.notify({
        title: message,
        type: 'loading',
        duration: null,
        progress: false,
        swipe: false,
        ...props
      })
    },

    group(props, key) {
      const existing = this.toasts.find(t => t.groupKey === key)

      if (existing) {
        existing.count = (existing.count || 1) + 1

        existing.meta = {
          ...(existing.meta || {}),
          count: existing.count
        }

        existing.currentX = 0
        existing.swiping = false

        if (existing.visible && existing.duration) {
          existing.stop()

          existing.pausedAt = null
          existing.progressValue = 1

          existing.start()
        }

        return existing
      }

      return this.addToast({
        ...props,
        groupKey: key,
        count: 1,
        meta: { count: 1 },
      })
    },

    promise(promise, messages = {}) {
      const toast = this.loading(messages.loading ?? 'Carregando...')
      const resolveMessage = (msg, data) => typeof msg === 'function' ? msg(data) : msg

      promise
        .then((data) => {
          if (!this.toasts.find(t => t.id === toast.id)) return

          this.updateToast(toast.id, {
            title: resolveMessage(messages.success, data) ?? 'Sucesso!',
            type: 'success',
            duration: getDynamicDuration(resolveMessage(messages.success, data)),
            progress: true,
            swipe: true,
          })
        })
        .catch((error) => {
          if (!this.toasts.find(t => t.id === toast.id)) return

          this.updateToast(toast.id, {
            title: resolveMessage(messages.error, error) ?? 'Erro!',
            type: 'danger',
            duration: getDynamicDuration(resolveMessage(messages.error, error)) * 1.3,
            progress: true,
            swipe: true,
          })
        })

      return promise
    },

    queue(props, max = 3) {
      const visible = this.toasts.filter(t => t.visible)

      if (visible.length >= max) {
        const oldest = visible.slice().sort((a, b) => a.createdAt - b.createdAt)[0]
        this.removeToast(oldest.id)
      }

      return this.addToast(props)
    },

    dedupe(props, windowMs = 2000) {
      const now = Date.now()

      const exists = this.toasts.find(t =>
        t.title === props.title && t.type === props.type &&
        now - t.createdAt < windowMs
      )

      if (exists) {
        return exists
      }

      return this.addToast({
        ...props,
        createdAt: now
      })
    }
  }
}

function normalizePosition(position = 'bottom-right') {
  if (position === 'top') return 'top-right'
  if (position === 'bottom') return 'bottom-right'
  return position
}

function getDynamicDuration(title = '', message = '') {
  const text = `${title} ${message}`.trim()

  const min = 3000
  const max = 9000
  const base = 1000

  const weightedLength =
    title.length * 1.2 +
    message.length * 1.6

  const readingSpeed = 16

  let time = base + (weightedLength / readingSpeed) * 1000

  const lines = text.split('\n').length
  time += lines * 300

  return Math.min(max, Math.max(min, time))
}

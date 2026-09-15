import { bind } from '../utils'

export function toast() {
  return {
    toasts: [],
    isPageVisible: false,
    isUserActive: false,
    idleTimeout: null,
    idleDelay: 0,
    _listeners: [],

    init() {
      bind(this.$el, {
        ['@toast.document'](e) {
          this.addToast(e.detail)
        },
        ['@toast-close.document'](e) {
          this.removeToast(e.detail.id)
        },
      })

      window.__tallkitToastReady = true
      ;(window.__tallkitToastQueue ?? []).forEach(({ event, detail }) => {
        if (event === 'toast') this.addToast(detail)
        if (event === 'toast-close') this.removeToast(detail.id)
      })
      window.__tallkitToastQueue = []

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
        if (this.idleTimeout) clearTimeout(this.idleTimeout)
        this.idleTimeout = setTimeout(markIdle, this.idleDelay)
      }

      this.resetIdleTimer()
    },

    destroy() {
      this._listeners.forEach((off) => off())
      clearTimeout(this.idleTimeout)
    },

    syncAttention() {
      const shouldRun = this.isPageVisible && this.isUserActive

      this.toasts.forEach((toast) => {
        if (!toast.duration || !toast.attentionAware) return

        if (shouldRun && toast.pausedAt) {
          toast.resume('attention')
        }

        if (!shouldRun && !toast.pausedAt) {
          toast.pause('attention')
        }
      })
    },

    addToast(props) {
      const position = normalizePosition(props.position)
      const maxStack = props.maxStack ?? 5

      if (maxStack !== false) {
        const sameSlot = this.toasts.filter((t) => t.position === position)

        if (sameSlot.length >= maxStack) {
          const oldest = sameSlot.slice().sort((a, b) => a.createdAt - b.createdAt)[0]

          if (oldest) {
            this.removeToast(oldest.id)
          }
        }
      }

      const duration = resolveDuration(props.duration, props.title, props.message, props.actions?.length > 0)
      const manager = this
      const currentToast = props.id ? this.toasts.find((t) => t.id === props.id) : null

      if (currentToast) {
        return this.updateToast(currentToast.id, props);
      }

      const toast = window.Alpine.reactive({
        id: crypto.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`,
        createdAt: Date.now(),
        ...props,
        duration,
        position,

        attentionAware: props.attentionAware ?? true,
        progress: props.progress ?? true,
        pauseOnHover: props.pauseOnHover ?? true,
        swipe: props.swipe ?? true,
        actions: normalizeActions(props.actions),

        visible: false,

        progressValue: 1,
        startTime: 0,
        total: duration,
        elapsedBeforePause: 0,
        raf: null,
        pausedAt: null,
        pausedByHover: false,
        pausedByAttention: false,

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
            if (!manager.toasts.find((t) => t.id === this.id)) {
              this.stop()
              return
            }

            if (this.pausedAt) return

            const elapsed = this.elapsedBeforePause + (time - this.startTime)
            const linear = Math.min(elapsed / this.total, 1)

            if (this.progress) {
              this.progressValue = 1 - linear
            }

            if (linear >= 1) {
              manager.removeToast(this.id)
              return
            }

            this.raf = requestAnimationFrame(loop)
          }

          this.raf = requestAnimationFrame(loop)
        },

        pause(reason = 'attention') {
          if (!this.duration) return

          if (reason === 'hover') {
            this.pausedByHover = true
          } else {
            this.pausedByAttention = true
          }

          if (this.pausedAt) return

          this.pausedAt = performance.now()
          this.elapsedBeforePause += this.pausedAt - this.startTime

          if (this.raf) {
            cancelAnimationFrame(this.raf)
            this.raf = null
          }
        },

        resume(reason = 'attention') {
          if (reason === 'hover') {
            this.pausedByHover = false
          } else {
            this.pausedByAttention = false
          }

          if (this.pausedByHover || this.pausedByAttention) return
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

          const width = (e.currentTarget).offsetWidth
          const threshold = width * 0.4

          if (Math.abs(this.currentX) > threshold) {
            manager.removeToast(this.id)
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
      const toast = this.toasts.find((t) => t.id === id)
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
        'invert',
        'actions',
      ];

      for (const key in data) {
        if (!allowed.includes(key) || key === 'duration') continue
        toast[key] = key === 'actions' ? normalizeActions(data[key]) : data[key]
      }

      toast.currentX = 0
      toast.swiping = false

      if (data.duration !== undefined) {
        toast.stop()

        toast.pausedAt = null
        toast.pausedByHover = false
        toast.pausedByAttention = false
        toast.duration = resolveDuration(data.duration, toast.title, toast.message, toast.actions?.length > 0)
        toast.total = toast.duration
        toast.elapsedBeforePause = 0

        toast.progressValue = 1

        if (toast.visible) {
          toast.start()
        }
      }

      return toast
    },

    removeToast(id) {
      const toast = this.toasts.find((t) => t.id === id)
      if (!toast) return

      toast.stop()
      toast.raf = null
      toast.visible = false

      setTimeout(() => {
        this.toasts = this.toasts.filter((t) => t.id !== id)
      }, 300)
    },

    getToastsByPosition(position) {
      return this.toasts.filter((t) => t.position === position);
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
        type: 'error',
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

    warning(message, props = {}) {
      return this.notify({
        title: message,
        type: 'warning',
        ...props
      })
    },

    loading(message, props = {}) {
      return this.notify({
        title: message,
        type: 'loading',
        duration: false,
        progress: false,
        swipe: false,
        ...props
      })
    },

    group(props, key) {
      const existing = this.toasts.find((t) => t.groupKey === key)

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
      const toast = this.loading(messages.loading ?? 'Loading...')
      const resolveMessage = (msg, data) =>
        typeof msg === 'function' ? msg(data) : msg

      promise
        .then((data) => {
          if (!this.toasts.find((t) => t.id === toast.id)) return

          this.updateToast(toast.id, {
            title: resolveMessage(messages.success, data) ?? 'Success!',
            type: 'success',
            duration: getDynamicDuration(resolveMessage(messages.success, data)),
            progress: true,
            swipe: true,
          })
        })
        .catch((error) => {
          if (!this.toasts.find((t) => t.id === toast.id)) return

          this.updateToast(toast.id, {
            title: resolveMessage(messages.error, error) ?? 'Error!',
            type: 'error',
            duration: getDynamicDuration(resolveMessage(messages.error, error)) * 1.3,
            progress: true,
            swipe: true,
          })
        })

      return promise
    },

    queue(props, max = 3) {
      const visible = this.toasts.filter((t) => t.visible)

      if (visible.length >= max) {
        const oldest = visible.slice().sort((a, b) => a.createdAt - b.createdAt)[0]
        this.removeToast(oldest.id)
      }

      return this.addToast(props)
    },

    dedupe(props, windowMs = 2000) {
      const now = Date.now()

      const exists = this.toasts.find((t) =>
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
  };
}

function normalizeActions(actions) {
  return (actions ?? []).map((action) => ({
    loading: false,
    ...action,
    run() {
      let result

      if (this.onClick) {
        result = this.onClick()
      } else if (this.method) {
        const component = window.Livewire?.find(this.component)

        if (!component) {
          console.warn(`[TALLKit] Toast action "${this.label}" could not find Livewire component "${this.component}" to call "${this.method}".`, this)
          return
        }

        result = component.call(this.method, ...normalizeParams(this.params))
      } else if (this.event) {
        window.Livewire?.dispatch(this.event, this.params ?? {})
        return
      } else {
        console.warn(`[TALLKit] Toast action "${this.label}" has no onClick, method, event, or href handler.`, this)
        return
      }

      if (result instanceof Promise) {
        this.loading = true
        result.finally(() => { this.loading = false })
      }
    },
  }))
}

function normalizeParams(params) {
  if (params == null) return []
  return Array.isArray(params) ? params : Object.values(params)
}

function resolveDuration(duration, title, message, hasActions = false) {
  if (duration === false) return null
  if (duration === true) return getDynamicDuration(title, message)
  if (duration == null) return hasActions ? null : getDynamicDuration(title, message)
  return duration
}

function normalizePosition(position) {
  position ??= 'bottom-right'

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
    (title?.length ?? 0) * 1.2 +
    (message?.length ?? 0) * 1.6

  const readingSpeed = 16

  let time = base + (weightedLength / readingSpeed) * 1000

  const lines = text.split('\n').length
  time += lines * 300

  return Math.min(max, Math.max(min, time))
}

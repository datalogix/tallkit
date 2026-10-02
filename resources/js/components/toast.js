import { bind, clamp, eventName, generateId, safeUrl, toMilliseconds } from '../utils'
import { sendToastEvent } from '../toast'

export function toast(flashed = [], texts = {}) {
  texts = { loading: 'Loading...', success: 'Success!', error: 'Error!', ...texts }

  return {
    toasts: [],

    isPageVisible: false,
    isUserActive: false,
    idleTimeout: null,
    idleDelay: 0,
    _listeners: [],

    init() {
      const active = window.__tallkitToastContainer

      if (active && active !== this.$el && active.isConnected) {
        console.warn('[tallkit] There is already a <tk:toast> on the page: this one stays inert.')
        flashed.forEach((detail) => sendToastEvent(eventName('toast'), detail))
        return
      }

      window.__tallkitToastContainer = this.$el

      bind(this.$el, {
        [`@${eventName('toast')}.document`](e) {
          this.addToast(e.detail)
        },
        [`@${eventName('toast-close')}.document`](e) {
          this.removeToast(e.detail.id)
        },
      })

      window.__tallkitToastReady = true
      ;(window.__tallkitToastQueue ?? []).forEach(({ event, detail }) => {
        if (event === eventName('toast')) this.addToast(detail)
        if (event === eventName('toast-close')) this.removeToast(detail.id)
      })
      window.__tallkitToastQueue = []

      flashed.forEach((detail) => this.addToast(detail))

      this.initAttentionListeners()
    },

    initAttentionListeners() {
      this.isPageVisible = !document.hidden
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

      if (window.__tallkitToastContainer === this.$el) {
        window.__tallkitToastContainer = null
        window.__tallkitToastReady = false
      }
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

      const currentToast = props.id ? this.toasts.find((t) => t.id === props.id) : null

      if (currentToast) {
        return this.updateToast(currentToast.id, props)
      }

      const toast = createToast(props, position, this)

      this.toasts.push(toast)
      this.announce(toast)

      this.$nextTick(() => {
        toast.visible = true
        toast.start()

        this.syncAttention()
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
      ]

      for (const key in data) {
        if (!allowed.includes(key) || key === 'duration') continue
        toast[key] = key === 'actions' ? normalizeActions(data[key]) : data[key]
      }

      // Without html: true a new title or message is text, whatever the old one was.
      if ('title' in data || 'message' in data) {
        toast.html = data.html === true
      }

      toast.resetSwipe()

      if ('title' in data || 'message' in data || 'type' in data) {
        this.announce(toast)
      }

      if (data.duration !== undefined) {
        toast.duration = resolveDuration(data.duration, toast.title, toast.message, toast.actions?.length > 0)
        toast.restart()
      }

      return toast
    },

    announce(toast) {
      const region = toast.type === 'error' ? this.$refs.assertiveRegion : this.$refs.politeRegion

      if (!region) return

      // A parsed document runs nothing: no scripts, no onerror.
      const text = (value) => toast.html
        ? new DOMParser().parseFromString(String(value ?? ''), 'text/html').body.textContent
        : String(value ?? '')
      const words = [text(toast.title), text(toast.message)].map((part) => part.trim()).filter(Boolean).join('. ')

      if (!words) return

      region.textContent = ''
      clearTimeout(region._tallkitAnnounce)
      region._tallkitAnnounce = setTimeout(() => { region.textContent = words }, 100)
    },

    // Text unless given as HTML on purpose: a name from data never turns into markup.
    showContent(el, value, html) {
      if (html) {
        el.innerHTML = value ?? ''
      } else {
        el.textContent = value ?? ''
      }
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
      return this.toasts.filter((t) => t.position === position)
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

        existing.resetSwipe()
        existing.restart()
        this.announce(existing)

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
      const toast = this.loading(messages.loading ?? texts.loading)
      const resolveMessage = (msg, data) =>
        typeof msg === 'function' ? msg(data) : msg

      promise
        .then((data) => {
          if (!this.toasts.find((t) => t.id === toast.id)) return

          this.updateToast(toast.id, {
            title: resolveMessage(messages.success, data) ?? texts.success,
            type: 'success',
            duration: getDynamicDuration(resolveMessage(messages.success, data)),
            progress: true,
            swipe: true,
          })
        })
        .catch((error) => {
          if (!this.toasts.find((t) => t.id === toast.id)) return

          this.updateToast(toast.id, {
            title: resolveMessage(messages.error, error) ?? texts.error,
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
  }
}

function createToast(props, position, manager) {
  const duration = resolveDuration(props.duration, props.title, props.message, props.actions?.length > 0)

  return window.Alpine.reactive({
    createdAt: Date.now(),
    ...props,
    id: props.id ?? generateId('toast'),
    duration,
    position,

    html: props.html === true,
    attentionAware: props.attentionAware ?? true,
    progress: props.progress ?? true,
    pauseOnHover: props.pauseOnHover ?? true,
    swipe: props.swipe ?? true,
    actions: normalizeActions(props.actions),

    visible: false,

    ...toastCountdown(duration, manager),
    ...toastSwipe(manager),
  })
}

const PAUSED_BY = { hover: 'pausedByHover', focus: 'pausedByFocus', attention: 'pausedByAttention' }

function toastCountdown(duration, manager) {
  return {
    progressValue: 1,
    startTime: 0,
    total: duration,
    elapsedBeforePause: 0,
    raf: null,
    pausedAt: null,
    pausedByHover: false,
    pausedByFocus: false,
    pausedByAttention: false,

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

      this[PAUSED_BY[reason] ?? PAUSED_BY.attention] = true

      if (this.pausedAt) return

      this.pausedAt = performance.now()
      this.elapsedBeforePause += this.pausedAt - this.startTime

      this.stop()
    },

    resume(reason = 'attention') {
      this[PAUSED_BY[reason] ?? PAUSED_BY.attention] = false

      if (this.pausedByHover || this.pausedByFocus || this.pausedByAttention) return
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

    restart() {
      this.stop()

      this.pausedAt = null
      this.elapsedBeforePause = 0
      this.total = this.duration
      this.progressValue = 1

      if (!this.visible || !this.duration) return

      this.start()

      if (this.pausedByHover) this.pause('hover')
      if (this.pausedByFocus) this.pause('focus')
      if (this.pausedByAttention) this.pause('attention')
    },
  }
}

function toastSwipe(manager) {
  return {
    swiping: false,
    startX: 0,
    startY: 0,
    currentX: 0,
    currentY: 0,
    lockDirection: null,

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

      if (!this.lockDirection && Math.max(Math.abs(this.currentX), Math.abs(this.currentY)) > 4) {
        this.lockDirection = Math.abs(this.currentX) > Math.abs(this.currentY) ? 'x' : 'y'

        // Captured only once sideways: from the start, a tap's click would go to the toast instead of the button.
        if (this.lockDirection === 'x') {
          e.currentTarget.setPointerCapture?.(e.pointerId)
        }
      }

      if (this.lockDirection === 'x') {
        e.preventDefault()
      }
    },

    onPointerCancel() {
      this.resetSwipe()
    },

    onPointerUp(e) {
      if (!this.swipe) return

      this.swiping = false

      if (this.lockDirection !== 'x') {
        this.resetSwipe()
        return
      }

      const width = (e.currentTarget).offsetWidth
      const threshold = width * 0.4

      if (Math.abs(this.currentX) > threshold) {
        manager.removeToast(this.id)
      } else {
        this.resetSwipe()
      }
    },

    resetSwipe() {
      this.swiping = false
      this.currentX = 0
      this.currentY = 0
      this.lockDirection = null
    },
  }
}

function normalizeActions(actions) {
  return (actions ?? []).map((action) => ({
    loading: false,
    ...action,
    // Only a page's address: actions may come from data.
    href: safeUrl(action.href),
    run() {
      let result

      if (this.onClick) {
        result = this.onClick()
      } else if (this.method) {
        const component = window.Livewire?.find(this.component)

        if (!component) {
          console.warn(`[tallkit] Toast action "${this.label}" could not find Livewire component "${this.component}" to call "${this.method}".`, this)
          return
        }

        result = component.call(this.method, ...normalizeParams(this.params))
      } else if (this.event) {
        window.Livewire?.dispatch(this.event, this.params ?? {})
        return
      } else {
        console.warn(`[tallkit] Toast action "${this.label}" has no onClick, method, event, or href handler.`, this)
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
  return toMilliseconds(duration, getDynamicDuration(title, message))
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

  return clamp(time, min, max)
}

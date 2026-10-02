import { queryData, queryAllData, bind, isRtl, isTypingIn, startInterval, emit, clamp, prefersReducedMotion } from '../utils'

// Reactive: controls set up before their carousel read it.
function registry() {
  return (window.__tallkitCarousels ??= window.Alpine.reactive({}))
}

export function carousel({ name = null, autoplay = false, interval = 5000, advance = 'slide', wrap = true, fade = false } = {}) {
  return {
    current: 0,
    slideCount: 0,
    visibleCount: 1,
    _autoplayId: null,
    _paused: false,
    _hovered: false,
    _focused: false,

    init() {
      this._root = this.$root

      if (name) {
        registry()[name] = this
      }

      this.measure()

      this.$nextTick(() => this.render())

      bind(this._root, {
        ['@keydown.arrow-left'](event) {
          if (isTypingIn(event.target)) return
          isRtl(this._root) ? this.next() : this.prev()
        },

        ['@keydown.arrow-right'](event) {
          if (isTypingIn(event.target)) return
          isRtl(this._root) ? this.prev() : this.next()
        },

        ['@mouseenter']() {
          this._hovered = true
        },

        ['@mouseleave']() {
          this._hovered = false
        },

        ['@focusin']() {
          this._focused = true
        },

        ['@focusout'](event) {
          if (!this._root.contains(event.relatedTarget)) this._focused = false
        },

        ['x-resize']() {
          this.measure()
          this.render()
        },
      })

      this.startAutoplay()
    },

    destroy() {
      clearInterval(this._autoplayId)

      if (name && registry()[name] === this) {
        delete registry()[name]
      }
    },

    slides() {
      return queryAllData(this._root, 'carousel-slide')
    },

    track() {
      return queryData(this._root, 'carousel-track')
    },

    measure() {
      const slides = this.slides()
      const track = this.track()

      this.slideCount = slides.length

      if (fade) {
        this.visibleCount = 1
        return
      }

      if (!slides.length || !track?.parentElement) {
        this.visibleCount = 1
        return
      }

      const rtl = isRtl(this._root)
      const viewportWidth = track.parentElement.getBoundingClientRect().width
      const first = slides[0].getBoundingClientRect()
      const start = rtl ? first.right : first.left

      let count = 0

      for (const slide of slides) {
        const rect = slide.getBoundingClientRect()

        if ((rtl ? start - rect.left : rect.right - start) > viewportWidth + 1) break

        count++
      }

      this.visibleCount = Math.max(1, count)
    },

    maxIndex() {
      return Math.max(0, this.slideCount - this.visibleCount)
    },

    step() {
      return advance === 'page' ? this.visibleCount : 1
    },

    pageCount() {
      return advance === 'page'
        ? Math.max(1, Math.ceil(this.slideCount / this.visibleCount))
        : this.maxIndex() + 1
    },

    currentPage() {
      return advance === 'page'
        ? Math.floor(this.current / this.visibleCount)
        : this.current
    },

    isPageActive(page) {
      return this.currentPage() === page
    },

    isFirst() {
      return !wrap && this.current <= 0
    },

    isLast() {
      return !wrap && this.current >= this.maxIndex()
    },

    slideNumber(el) {
      return this.slides().indexOf(el) + 1
    },

    isSlideVisible(el) {
      const index = this.slides().indexOf(el)

      if (index === -1) return false

      return fade
        ? index === this.current
        : index >= this.current && index < this.current + this.visibleCount
    },

    next() {
      this.goTo(this.current + this.step())
    },

    prev() {
      this.goTo(this.current - this.step())
    },

    goToPage(page) {
      this.goTo(advance === 'page' ? page * this.visibleCount : page)
    },

    goTo(index) {
      const max = this.maxIndex()
      const previous = this.current

      this.current = wrap && max > 0
        ? ((index % (max + 1)) + (max + 1)) % (max + 1)
        : clamp(index, 0, max)

      this.render()
      this.resetAutoplay()

      if (this.current !== previous) {
        emit(queryData(this.$root, 'carousel-viewport'), 'changed', { index: this.current })
      }
    },

    render() {
      const slides = this.slides()

      if (fade) {
        slides.forEach((slide, index) => {
          const active = index === this.current

          slide.style.opacity = active ? '1' : '0'
          slide.toggleAttribute('data-active', active)
          slide.style.pointerEvents = active ? '' : 'none'
        })

        return
      }

      const track = this.track()
      const target = slides[this.current]

      if (!track || !target) return

      const rtl = isRtl(this._root)
      const trackRect = track.getBoundingClientRect()
      const targetRect = target.getBoundingClientRect()
      const offset = rtl ? trackRect.right - targetRect.right : targetRect.left - trackRect.left

      track.style.transform = `translateX(${rtl ? offset : -offset}px)`
    },

    autoplays() {
      return autoplay && !prefersReducedMotion()
    },

    startAutoplay() {
      if (!this.autoplays()) return

      this._autoplayId = startInterval(() => {
        if (!this._paused && !this._hovered && !this._focused) this.next()
      }, interval)
    },

    resetAutoplay() {
      if (!autoplay) return

      clearInterval(this._autoplayId)
      this.startAutoplay()
    },

    pause() {
      this._paused = true
    },

    resume() {
      this._paused = false
    },

    isPaused() {
      return this._paused
    },

    togglePause() {
      this._paused = !this._paused
    },
  }
}

export function carouselControls({ name = null } = {}) {
  return {
    target() {
      return registry()[name] ?? null
    },

    next() {
      this.target()?.next()
    },

    prev() {
      this.target()?.prev()
    },

    goToPage(page) {
      this.target()?.goToPage(page)
    },

    pageCount() {
      return this.target()?.pageCount() ?? 0
    },

    isPageActive(page) {
      return !!this.target()?.isPageActive(page)
    },

    isFirst() {
      return this.target()?.isFirst() ?? true
    },

    isLast() {
      return this.target()?.isLast() ?? true
    },
  }
}

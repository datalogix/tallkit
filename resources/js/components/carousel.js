import { queryData, queryAllData, bind, isRtl, isTypingIn, startInterval, emit, prefersReducedMotion } from '../utils'

function registry() {
  return (window.__tallkitCarousels ??= window.Alpine.reactive({}))
}

const SWIPE_THRESHOLD = 50
const DRAG_LOCK = 10
const AUTOPLAY_TICK = 100

export function carousel({ name = null, autoplay = false, interval = 5000, advance = 'slide', wrap = true, fade = false } = {}) {
  let offset = 0
  let drag = null
  let lastTick = 0

  return {
    current: 0,
    slideCount: 0,
    // Cached by measure(): reactive, so slides already rendered see additions and removals.
    slideList: [],
    visibleCount: 1,
    elapsed: 0,
    _autoplayId: null,
    _paused: false,
    _hovered: false,
    _focused: false,
    _hidden: false,
    _offscreen: false,

    init() {
      this._root = this.$root

      if (name) {
        registry()[name] = this
      }

      this.measure()

      this.$nextTick(() => this.render({ instant: true }))

      this.bindDrag()

      // Slides added or removed later (a Livewire morph, an x-for) are counted again.
      this._slidesObserver = new MutationObserver(() => {
        this.measure()
        this.current = Math.min(this.current, this.maxIndex())
        this.render({ instant: true })
      })

      const track = this.track()

      if (track) this._slidesObserver.observe(track, { childList: true })

      // Autoplay holds while the tab is hidden or the carousel is scrolled away.
      this._onVisibilityChange = () => {
        this._hidden = document.hidden
      }

      document.addEventListener('visibilitychange', this._onVisibilityChange)

      this._visibilityObserver = new IntersectionObserver(([entry]) => {
        this._offscreen = !entry.isIntersecting
      })

      this._visibilityObserver.observe(this._root)

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
          this.render({ instant: true })
        },
      })

      this.startAutoplay()
    },

    destroy() {
      clearInterval(this._autoplayId)
      this._slidesObserver?.disconnect()
      this._visibilityObserver?.disconnect()
      document.removeEventListener('visibilitychange', this._onVisibilityChange)

      if (name && registry()[name] === this) {
        delete registry()[name]
      }
    },

    // Touch and pen only: a mouse drag would fight text selection and clicks on links.
    bindDrag() {
      const viewport = queryData(this._root, 'carousel-viewport')

      bind(viewport, {
        ['@pointerdown'](event) {
          if (event.pointerType === 'mouse' || !this.canNavigate()) return

          drag = { id: event.pointerId, x: event.clientX, y: event.clientY, dx: 0, locked: false }
        },

        ['@pointermove'](event) {
          if (!drag || event.pointerId !== drag.id) return

          const dx = event.clientX - drag.x
          const dy = event.clientY - drag.y

          if (!drag.locked) {
            if (Math.max(Math.abs(dx), Math.abs(dy)) < DRAG_LOCK) return

            // Mostly vertical: a page scroll, left to the browser.
            if (Math.abs(dy) > Math.abs(dx)) {
              drag = null
              return
            }

            drag.locked = true
            viewport.setPointerCapture?.(event.pointerId)
          }

          drag.dx = dx

          if (fade) return

          const track = this.track()
          const forward = (dx < 0) !== isRtl(this._root)
          const atEdge = !wrap && (forward ? this.current >= this.maxIndex() : this.current <= 0)

          // Resists past the ends, so the track can't be pulled away from them.
          track.style.transition = 'none'
          track.style.transform = `translateX(${offset + (atEdge ? dx / 3 : dx)}px)`
        },

        ['@pointerup'](event) {
          if (!drag || event.pointerId !== drag.id) return

          const { dx, locked } = drag

          drag = null

          if (!locked) return

          this.track().style.transition = ''

          if (Math.abs(dx) < SWIPE_THRESHOLD) {
            this.render()
            return
          }

          // Swiping toward the start shows what comes after.
          const forward = (dx < 0) !== isRtl(this._root)

          forward ? this.next() : this.prev()
        },

        ['@pointercancel']() {
          if (!drag) return

          const { locked } = drag

          drag = null

          if (!locked) return

          this.track().style.transition = ''
          this.render()
        },
      })
    },

    slides() {
      return this.slideList
    },

    track() {
      return queryData(this._root, 'carousel-track')
    },

    measure() {
      this.slideList = queryAllData(this._root, 'carousel-slide')

      const slides = this.slideList
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
        ? Math.ceil(this.current / this.visibleCount)
        : this.current
    },

    isPageActive(page) {
      return this.currentPage() === page
    },

    canNavigate() {
      return this.pageCount() > 1
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

    isIndexVisible(index) {
      return fade
        ? index === this.current
        : index >= this.current && index < this.current + this.visibleCount
    },

    isSlideVisible(el) {
      const index = this.slides().indexOf(el)

      return index !== -1 && this.isIndexVisible(index)
    },

    thumbnailOf(slide) {
      return slide.dataset.thumbnail || slide.querySelector('img')?.getAttribute('src') || null
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

      // Past an end: the other end, but only once this one was reached; a page step stops at it first.
      let target = Math.min(Math.max(index, 0), max)
      let wrapped = false

      if (wrap && max > 0) {
        if (index > max && previous >= max) {
          target = 0
          wrapped = true
        } else if (index < 0 && previous <= 0) {
          target = max
          wrapped = true
        }
      }

      this.current = target

      // Wrapping jumps: sliding back across every slide reads as a rewind.
      this.render({ instant: wrapped })
      this.resetAutoplay()

      if (this.current !== previous) {
        emit(queryData(this.$root, 'carousel-viewport'), 'changed', { index: this.current })
        this.$nextTick(() => this.revealThumbnail())
      }
    },

    render({ instant = false } = {}) {
      const slides = this.slides()

      this.preload()

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
      const distance = rtl ? trackRect.right - targetRect.right : targetRect.left - trackRect.left

      offset = rtl ? distance : -distance

      if (instant) track.style.transition = 'none'

      track.style.transform = `translateX(${offset}px)`

      if (instant) {
        // Flushes the jump before the transition comes back.
        void track.offsetWidth
        track.style.transition = ''
      }
    },

    // Lazy images in the visible slides and their neighbours load now, so they are ready when shown.
    preload() {
      const slides = this.slides()
      const count = slides.length

      if (!count) return

      for (let index = this.current - 1; index <= this.current + this.visibleCount; index++) {
        const slide = slides[(index + count) % count]

        slide?.querySelectorAll('img[loading="lazy"]').forEach((img) => {
          img.loading = 'eager'
        })
      }
    },

    // Scrolls the strip itself: scrollIntoView() could scroll the page as well.
    revealThumbnail() {
      const strip = queryData(this._root, 'carousel-thumbnails')
      const thumb = strip?.querySelectorAll(':scope > button')[this.current]

      if (!thumb || strip.scrollWidth <= strip.clientWidth) return

      const stripRect = strip.getBoundingClientRect()
      const thumbRect = thumb.getBoundingClientRect()

      strip.scrollBy({
        left: (thumbRect.left + thumbRect.width / 2) - (stripRect.left + stripRect.width / 2),
        behavior: prefersReducedMotion() ? 'auto' : 'smooth',
      })
    },

    autoplays() {
      return autoplay && !prefersReducedMotion()
    },

    // Counts only the time spent rotating, so a pause resumes where it stopped.
    startAutoplay() {
      if (!this.autoplays()) return

      lastTick = performance.now()

      this._autoplayId = startInterval(() => {
        const now = performance.now()
        const delta = now - lastTick

        lastTick = now

        if (!this.isRotating() || !this.canNavigate()) return

        this.elapsed = Math.min(this.elapsed + delta, interval)

        if (this.elapsed >= interval) this.next()
      }, AUTOPLAY_TICK)
    },

    resetAutoplay() {
      if (!autoplay) return

      this.elapsed = 0
      clearInterval(this._autoplayId)
      this.startAutoplay()
    },

    progress() {
      return interval > 0 ? this.elapsed / interval : 0
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

    isRotating() {
      return this.autoplays() && !this._paused && !this._hovered && !this._focused && !this._hidden && !this._offscreen
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

    currentPage() {
      return this.target()?.currentPage() ?? 0
    },

    isPageActive(page) {
      return !!this.target()?.isPageActive(page)
    },

    canNavigate() {
      return !!this.target()?.canNavigate()
    },

    isFirst() {
      return this.target()?.isFirst() ?? true
    },

    isLast() {
      return this.target()?.isLast() ?? true
    },
  }
}

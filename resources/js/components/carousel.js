import { bind, dataKey, interval as createInterval } from '../utils'

export function carousel({ name = null, autoplay = false, interval = 5000, advance = 'slide', wrap = true, fade = false } = {}) {
  return {
    current: 0,
    slideCount: 0,
    visibleCount: 1,
    _autoplayId: null,
    _paused: false,

    init() {
      this._root = this.$root

      if (name) {
        window.__tallkitCarousels ??= {}
        window.__tallkitCarousels[name] = this
      }

      this.measure()

      this.$nextTick(() => this.render())

      bind(this._root, {
        ['@keydown.arrow-left']() {
          this.prev()
        },

        ['@keydown.arrow-right']() {
          this.next()
        },

        ['@mouseenter']() {
          this.pause()
        },

        ['@mouseleave']() {
          this.resume()
        },

        ['@focusin']() {
          this.pause()
        },

        ['@focusout']() {
          this.resume()
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

      if (name && window.__tallkitCarousels?.[name] === this) {
        delete window.__tallkitCarousels[name]
      }
    },

    slides() {
      return Array.from(this._root.querySelectorAll(dataKey('carousel-slide')))
    },

    track() {
      return this._root.querySelector(dataKey('carousel-track'))
    },

    measure() {
      const slides = this.slides()
      const track = this.track()

      this.slideCount = slides.length

      if (fade) {
        // Fade mode stacks every slide in the same grid cell (`grid-area:1/1`), so
        // they all share the same bounding rect — geometry-based counting below
        // would count them all as "visible". Fade always shows exactly one slide.
        this.visibleCount = 1
        return
      }

      if (!slides.length || !track?.parentElement) {
        this.visibleCount = 1
        return
      }

      const viewportWidth = track.parentElement.getBoundingClientRect().width
      const trackLeft = slides[0].getBoundingClientRect().left

      let count = 0

      for (const slide of slides) {
        const rect = slide.getBoundingClientRect()

        if (rect.right - trackLeft > viewportWidth + 1) break

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

      this.current = wrap && max > 0
        ? ((index % (max + 1)) + (max + 1)) % (max + 1)
        : Math.max(0, Math.min(max, index))

      this.render()
      this.resetAutoplay()
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

      const offset = target.getBoundingClientRect().left - track.getBoundingClientRect().left

      track.style.transform = `translateX(-${offset}px)`
    },

    startAutoplay() {
      if (!autoplay) return

      this._autoplayId = createInterval(() => {
        if (!this._paused) this.next()
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
  };
}

export function carouselControls({ name = null } = {}) {
  return {
    target() {
      return window.__tallkitCarousels?.[name] ?? null
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
  };
}

import { loadable } from './loadable'

export function fullCalendar({ locale, ...options } = {}) {
  return {
    ...loadable(),

    fullCalendar: null,

    init() {
      this.load(async () => {
        if (!window.FullCalendar) {
          await this.$tallkit.loadScript([
            'https://cdn.jsdelivr.net/npm/fullcalendar@6/index.global.min.js',
            locale && locale !== 'en'
              ? `https://cdn.jsdelivr.net/npm/@fullcalendar/core@6/locales/${locale}.global.min.js`
              : 'https://cdn.jsdelivr.net/npm/@fullcalendar/core@6/locales-all.global.min.js'
          ])
        }
      })
    },

    getDataOptions() {
      return window.Alpine.evaluate(this.$el, this.$el.getAttribute('data-options') || '{}')
    },

    render() {
      this.fullCalendar ??= new window.FullCalendar.Calendar(this.$el, { ...options, ...this.getDataOptions() })
      this.fullCalendar.render()
      this.$dispatch('rendered', { fullCalendar: this.fullCalendar })
    }
  }
}

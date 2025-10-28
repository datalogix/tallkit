
export function fullCalendar(options: {
  locale?: string
  [key: string]: any
}) {
  return {
    fullCalendar: null,

    getOptions() {
      return window.Alpine.evaluate(this.$el, this.$el.getAttribute('data-options') || '{}')
    },

    async init() {
      if (!window.FullCalendar) {
        await this.$tallkit.loadScript([
          'https://cdn.jsdelivr.net/npm/fullcalendar@6/index.global.min.js',
          options.locale && options.locale !== 'en'
            ? `https://cdn.jsdelivr.net/npm/@fullcalendar/core@6/locales/${options.locale}.global.min.js`
            : 'https://cdn.jsdelivr.net/npm/@fullcalendar/core@6/locales-all.global.min.js'
        ])
      }

      this.fullCalendar = new window.FullCalendar.Calendar(this.$el, {
        ...options,
        ...this.getOptions(),
      })

      this.fullCalendar.render()
    }
  }
}

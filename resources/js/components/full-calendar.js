import { loadRemoteAssets } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { loadable } from './loadable'

export function fullCalendar({ locale = null, theme = null, palette = null, options = {} } = {}) {
  const _loadable = loadable()

  return {
    ..._loadable,
    ...dataOptions(),

    fullCalendar: null,

    init() {
      const baseUrl = 'https://cdn.jsdelivr.net/npm/fullcalendar@7'
      const scripts = [`${baseUrl}/all/global.min.js`]

      if (locale && locale !== 'en') {
        scripts.push(`${baseUrl}/locales/${String(locale).replace('_', '-').toLowerCase()}/global.min.js`)
      }

      scripts.push(`${baseUrl}/themes/${theme ?? 'monarch'}/global.js`)

      this.load(() => loadRemoteAssets(() => !!window.FullCalendar, scripts, [
        `${baseUrl}/skeleton.css`,
        `${baseUrl}/themes/${theme ?? 'monarch'}/theme.css`,
        `${baseUrl}/themes/${theme ?? 'monarch'}/palettes/${palette ?? 'blue'}.css`,
      ]))
    },

    render() {
      try {
        this.fullCalendar?.destroy()
        this.fullCalendar = new window.FullCalendar.Calendar(this.$el, {
          locale,
          ...options,
          ...this.getDataOptions()
        })
        this.fullCalendar.render()
        this.$dispatch('rendered', { fullCalendar: this.fullCalendar })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.fullCalendar?.destroy()
      this.fullCalendar = null
    }
  }
}

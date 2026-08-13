import { loadRemoteAssets } from '../utils'
import { dataOptions } from './data-options'
import { loadable } from './loadable'

export function fullCalendar({ locale = null, theme = null, palette = null, options = {} } = {}) {
  const _loadable = loadable()

  return {
    ..._loadable,
    ...dataOptions(),

    fullCalendar: null,

    init() {
      const baseUrl = 'https://cdn.jsdelivr.net/npm/fullcalendar@7'

      this.load(() => loadRemoteAssets(() => !!window.FullCalendar, [
        `${baseUrl}/all/global.min.js`,
        locale && locale !== 'en'
          ? `${baseUrl}/locales/${String(locale).replace('_', '-').toLowerCase()}/global.min.js`
          : `${baseUrl}/locales-all/global.min.js`,
        `${baseUrl}/themes/${theme ?? 'monarch'}/global.js`,
      ], [
        `${baseUrl}/skeleton.css`,
        `${baseUrl}/themes/${theme ?? 'monarch'}/theme.css`,
        `${baseUrl}/themes/${theme ?? 'monarch'}/palettes/${palette ?? 'blue'}.css`,
      ]))
    },

    render() {
      this.fullCalendar?.destroy()
      this.fullCalendar = new window.FullCalendar.Calendar(this.$el, {
        locale,
        ...options,
        ...this.getDataOptions()
      })
      this.fullCalendar.render()
      this.$dispatch('rendered', { fullCalendar: this.fullCalendar })
    },

    destroy() {
      _loadable.destroy.call(this)
      this.fullCalendar?.destroy()
      this.fullCalendar = null
    }
  }
}

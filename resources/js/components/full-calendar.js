import { loadRemoteAssets, loadScript, isDarkMode, onColorSchemeChange, emit } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { serverOptions } from '../mixins/server-options'
import { loadable } from './loadable'

export function fullCalendar({ locale = null, theme = null, palette = null, options = {} } = {}) {
  const _loadable = loadable()

  let calendar = null

  const calendarLocale = () => (locale ? String(locale).replace('_', '-').toLowerCase() : null)

  return {
    ..._loadable,
    ...dataOptions(),
    ...serverOptions(),

    _stopColorScheme: null,

    getCalendar() {
      return calendar
    },

    init() {
      const syncScheme = (dark) => this.$root.setAttribute('data-color-scheme', dark ? 'dark' : 'light')
      syncScheme(isDarkMode())
      this._stopColorScheme = onColorSchemeChange(syncScheme)

      this.followServerOptions((next) => {
        options = next

        if (calendar) this.render()
      })

      const baseUrl = 'https://cdn.jsdelivr.net/npm/fullcalendar@7'

      this.load(async () => {
        await loadRemoteAssets(() => !!window.FullCalendar, [
          `${baseUrl}/all/global.min.js`,
          `${baseUrl}/themes/${theme ?? 'monarch'}/global.js`,
        ], [
          `${baseUrl}/skeleton.css`,
          `${baseUrl}/themes/${theme ?? 'monarch'}/theme.css`,
          `${baseUrl}/themes/${theme ?? 'monarch'}/palettes/${palette ?? 'blue'}.css`,
        ])

        const code = calendarLocale()

        if (code && code !== 'en' && code !== 'en-us') {
          const file = (name) => loadScript(`${baseUrl}/locales/${name}/global.min.js`)

          await file(code).catch(() => (code.includes('-') ? file(code.split('-')[0]) : null)).catch(() => null)
        }
      })
    },

    render() {
      try {
        this._calendarEl ??= this.$el

        const current = calendar && { initialDate: calendar.getDate(), initialView: calendar.view.type }

        calendar?.destroy()
        calendar = new window.FullCalendar.Calendar(this._calendarEl, {
          locale: calendarLocale() || undefined,
          ...options,
          ...this.getDataOptions(this._calendarEl),
          ...current,
        })
        calendar.render()
        emit(this._calendarEl, 'rendered', { fullCalendar: calendar }, { later: true })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.stopFollowingServerOptions()
      this._stopColorScheme?.()
      calendar?.destroy()
      calendar = null
    }
  }
}

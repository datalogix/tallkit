import { dataKey, bind } from '../utils'

export function notification({ channel = null } = {}) {
  return {
    init() {
      bind(this.$el.querySelectorAll(dataKey('notification-mark-all')), {
        ['@click'](e) {
          const button = e.currentTarget as HTMLElement
          const scope = button.closest('[role=tabpanel]') ?? this.$el

          scope
            .querySelectorAll(dataKey('notification-item'))
            .forEach((el) => el.dispatchEvent(new CustomEvent('dismiss')))
        },
      })

      if (!channel || !window.Echo || !this.$wire) {
        return
      }

      window.Echo.private(channel).notification(() => {
        this.$wire.$refresh()
      })
    },

    destroy() {
      if (channel && window.Echo) {
        window.Echo.leave(channel)
      }
    },
  }
}

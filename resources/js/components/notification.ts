import { dataKey, bind } from '../utils'

export function notification({ channel = null } = {}) {
  return {
    init() {
      bind(this.$el.querySelectorAll(dataKey('notification-mark-all')), {
        ['@click']() {
          this.$el.closest('[role=tabpanel]')
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

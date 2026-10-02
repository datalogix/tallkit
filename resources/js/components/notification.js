import { queryAllData, bind, emit, eventName } from '../utils'

// For an Echo with no stopListeningForNotification().
const NOTIFICATION_EVENT = '.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated'

// Counted: the channel is left when the last listener goes, not the first.
const listening = new Map()

let warned = false

export function notification({ channel = null } = {}) {
  return {
    _onNotification: null,

    init() {
      bind(queryAllData(this.$el, 'notification-mark-all'), {
        ['@click'](e) {
          const scope = e.currentTarget.closest('[role=tabpanel]') ?? this.$el

          queryAllData(scope, 'notification-item').forEach((el) => emit(el, eventName('dismiss')))
        },
      })

      if (!channel || !this.$wire) return

      if (!window.Echo) {
        if (!warned) console.warn('[tallkit] <tk:notification echo> needs Laravel Echo on the page (window.Echo).')
        warned = true
        return
      }

      this._onNotification = () => this.$wire.$refresh()

      window.Echo.private(channel).notification(this._onNotification)
      listening.set(channel, (listening.get(channel) ?? 0) + 1)
    },

    destroy() {
      if (!this._onNotification || !window.Echo) return

      const subscription = window.Echo.private(channel)

      // Echo 2 has its own call; an older one takes the event's name.
      subscription.stopListeningForNotification
        ? subscription.stopListeningForNotification(this._onNotification)
        : subscription.stopListening(NOTIFICATION_EVENT, this._onNotification)
      this._onNotification = null

      const left = (listening.get(channel) ?? 1) - 1

      if (left > 0) {
        listening.set(channel, left)
      } else {
        listening.delete(channel)
        window.Echo.leave(channel)
      }
    },
  }
}

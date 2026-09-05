import { dismissible } from '../mixins/dismissible'

export function notificationItem() {
  return {
    ...dismissible('collapse'),
  }
}

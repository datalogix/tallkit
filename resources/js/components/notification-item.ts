import { dismissible } from './dismissible'

export function notificationItem() {
  return {
    ...dismissible('collapse'),
  }
}

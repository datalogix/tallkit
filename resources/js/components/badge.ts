import { dismissible } from './dismissible'

export function badge() {
  return {
    ...dismissible('fade'),
  }
}

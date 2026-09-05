import { dismissible } from '../mixins/dismissible'

export function badge() {
  return {
    ...dismissible('fade'),
  }
}

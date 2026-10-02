import { checkAll } from '../mixins/check-all'

export function switchAll({ group = null } = {}) {
  return checkAll('switch', group ?? '')
}

import { groupAll } from '../mixins/group-all'

export function switchAll({ group = null } = {}) {
  return groupAll('switch', group ?? '')
}

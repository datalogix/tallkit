import { groupAll } from '../mixins/group-all'

export function toggleAll({ group = null } = {}) {
  return groupAll('toggle', group ?? '')
}

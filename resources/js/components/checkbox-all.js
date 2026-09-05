import { groupAll } from '../mixins/group-all'

export function checkboxAll({ group = '' } = {}) {
  return groupAll('checkbox', group)
}

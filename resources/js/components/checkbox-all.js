import { checkAll } from '../mixins/check-all'

export function checkboxAll({ group = '' } = {}) {
  return checkAll('checkbox', group)
}

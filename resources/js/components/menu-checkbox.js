import { menuItem } from '../mixins/menu-item'

export function menuCheckbox(checked) {
  return menuItem(checked, 'checkbox')
}

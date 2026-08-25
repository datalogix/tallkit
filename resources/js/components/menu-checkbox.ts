import { menuItem } from '../mixins/menu-item'

export function menuCheckbox(checked?: boolean) {
  return menuItem(checked, 'checkbox')
}

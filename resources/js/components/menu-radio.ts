import { menuItem } from '../mixins/menu-item'

export function menuRadio(checked?: boolean) {
  return menuItem(checked, 'radio')
}

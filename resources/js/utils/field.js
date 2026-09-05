import { dataKey } from './string'

export function setFieldValue(
  el,
  value
) {
  if (!el) return

  el.value = value?.toString() ?? ''
  el.dispatchEvent(new Event('input', { bubbles: true }))
  el.dispatchEvent(new Event('change', { bubbles: true }))
}

export function setFieldChecked(
  el,
  checked
) {
  if (!el || el.checked === checked) return

  el.checked = checked
  el.dispatchEvent(new Event('input', { bubbles: true }))
  el.dispatchEvent(new Event('change', { bubbles: true }))
}

export function findInField(el, childKey, ancestorKey = 'field') {
  return el
    ?.closest(dataKey(ancestorKey))
    ?.querySelector(dataKey(childKey)) ?? null
}

export function findFieldInput(el) {
  return findInField(el, 'input', 'field-control');
}

export function allChecked(items, getChecked) {
  return items.length > 0 && items.every(getChecked)
}

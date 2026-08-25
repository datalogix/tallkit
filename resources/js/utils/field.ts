import { dataKey } from './string'

export function setFieldValue(
  el: HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement | null | undefined,
  value: string | number | null | undefined
) {
  if (!el) return

  el.value = value?.toString() ?? ''
  el.dispatchEvent(new Event('input', { bubbles: true }))
  el.dispatchEvent(new Event('change', { bubbles: true }))
}

export function setFieldChecked(
  el: HTMLInputElement | null | undefined,
  checked: boolean
) {
  if (!el || el.checked === checked) return

  el.checked = checked
  el.dispatchEvent(new Event('input', { bubbles: true }))
  el.dispatchEvent(new Event('change', { bubbles: true }))
}

export function findInField(
  el: Element | null | undefined,
  childKey: string,
  ancestorKey: string = 'field'
): HTMLElement | null {
  return el
    ?.closest(dataKey(ancestorKey))
    ?.querySelector(dataKey(childKey)) ?? null
}

export function findFieldInput(el: Element | null | undefined): HTMLInputElement | null {
  return findInField(el, 'input', 'field-control') as HTMLInputElement | null
}

export function allChecked<T>(items: T[], getChecked: (item: T) => boolean): boolean {
  return items.length > 0 && items.every(getChecked)
}

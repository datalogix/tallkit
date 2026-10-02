import { dataSelector, queryData } from './naming'

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
  return queryData(el?.closest(dataSelector(ancestorKey)), childKey)
}

export function findFieldInput(el) {
  return findInField(el, 'input', 'field-control')
}

export function allChecked(items, getChecked) {
  return items.length > 0 && items.every(getChecked)
}

const labelTargets = new Map()

function onLabelClick(e) {
  const label = e.target?.closest?.('label')
  const target = label?.htmlFor ? document.getElementById(label.htmlFor) : null
  const focus = target && labelTargets.get(target)

  if (!focus) return

  e.preventDefault()
  focus()
}

export function focusOnLabelClick(target, focus) {
  if (!target?.id) return () => {}

  if (labelTargets.size === 0) document.addEventListener('click', onLabelClick)

  labelTargets.set(target, focus)

  return () => {
    labelTargets.delete(target)

    if (labelTargets.size === 0) document.removeEventListener('click', onLabelClick)
  }
}

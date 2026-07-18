export function setFieldValue(
  el: HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement | null | undefined,
  value: string | number | null | undefined
) {
  if (!el) return

  el.value = value?.toString() ?? ''
  el.dispatchEvent(new Event('input', { bubbles: true }))
  el.dispatchEvent(new Event('change', { bubbles: true }))
}

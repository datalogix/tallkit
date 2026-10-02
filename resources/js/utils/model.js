export function getWireModelInfo(element) {
  if (!element) return null

  for (const attr of element.attributes) {
    if (attr.name.startsWith('wire:model')) {
      const modifier = attr.name.includes('.') ? attr.name.split('.').slice(1).join('.') : ''

      return {
        name: attr.value,
        modifier: modifier
      }
    }
  }

  return null
}

export function hasBlurModel(field) {
  return !!field && [...field.attributes].some((attr) => /^(wire:model|x-model)\b/.test(attr.name) && /\.blur\b/.test(attr.name))
}

// A field that is never focused never blurs: .blur models listen on the component's root.
export function blurOnFocusLeave(root, field, onBlur = () => field.dispatchEvent(new Event('blur'))) {
  if (!root || !field) return

  root.addEventListener('focusout', (e) => {
    if (e.target === field) return
    if (e.relatedTarget && root.contains(e.relatedTarget)) return

    onBlur()
  })
}

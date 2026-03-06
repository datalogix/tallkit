export function getWireModelInfo(element: HTMLElement) {
  for (let attr of element.attributes) {
    if (attr.name.startsWith('wire:model')) {
      let modifier = attr.name.includes('.') ? attr.name.split('.').slice(1).join('.') : ''

      return {
        name: attr.value,
        modifier: modifier
      }
    }
  }

  return null
}

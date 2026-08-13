export function getWireModelInfo(element: HTMLElement) {
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

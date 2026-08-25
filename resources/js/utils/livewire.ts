export function hasLivewire(): boolean {
  return !!window.Livewire
}

export function onLivewireCommit(handler: (payload: any) => void): () => void {
  const off = window.Livewire?.hook('commit', handler)

  return typeof off === 'function' ? off : () => {}
}

export function hasLivewire() {
  return !!window.Livewire
}

export function onLivewireCommit(handler) {
  const off = window.Livewire?.hook('commit', handler)

  return typeof off === 'function' ? off : () => {}
}

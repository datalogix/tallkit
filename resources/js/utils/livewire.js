import { dataKey } from './naming'
import { emit } from './event'

export function hasLivewire() {
  return !!window.Livewire
}

export function onLivewireCommit(handler) {
  const off = window.Livewire?.hook('commit', handler)

  return typeof off === 'function' ? off : () => {}
}

// An update puts the server's attributes back: the ones set here on a matching element are kept.
export function keepAttributesOnMorph(match, names) {
  const off = window.Livewire?.hook('morph.updating', ({ el, toEl }) => {
    if (!toEl?.setAttribute || !el?.getAttribute || !match(el)) return

    for (const name of names) {
      const value = el.getAttribute(name)
      if (value !== null) toEl.setAttribute(name, value)
    }
  })

  return typeof off === 'function' ? off : () => {}
}

let skippingDismissed = false

// Left out of the morph, or Livewire's next update would put it back.
export function keepDismissed(el) {
  el.hidden = true
  el.setAttribute(dataKey('dismissed'), '')

  if (skippingDismissed || !window.Livewire) return

  skippingDismissed = true

  window.Livewire.hook('morph.updating', ({ el: target, toEl, skip }) => {
    if (!target?.hasAttribute?.(dataKey('dismissed'))) return

    const html = toEl?.outerHTML ?? null

    target.__tallkitDismissedHtml ??= html

    if (html !== null && html !== target.__tallkitDismissedHtml) {
      target.removeAttribute(dataKey('dismissed'))
      target.hidden = false
      delete target.__tallkitDismissedHtml

      emit(target, 'restored')

      return
    }

    skip()
  })
}

let syncingIgnoredFields = false

// wire:ignore keeps the field's error state out of Livewire's updates too: copied over here.
export function syncIgnoredFieldState() {
  if (syncingIgnoredFields || !window.Livewire) return

  syncingIgnoredFields = true

  const attributes = ['aria-invalid', 'data-invalid', 'aria-describedby']

  window.Livewire.hook('morph.updating', ({ el, toEl }) => {
    if (!el?.hasAttribute?.('wire:ignore') || !toEl?.querySelectorAll) return

    const pairs = [[el, toEl]]

    for (const to of toEl.querySelectorAll('[id]')) {
      const from = el.querySelector(`#${CSS.escape(to.id)}`)
      if (from) pairs.push([from, to])
    }

    for (const [from, to] of pairs) {
      for (const name of attributes) {
        const value = to.getAttribute(name)

        if (value === null) {
          from.removeAttribute(name)
        } else if (from.getAttribute(name) !== value) {
          from.setAttribute(name, value)
        }
      }
    }
  })
}

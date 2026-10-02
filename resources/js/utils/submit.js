import { dataKey, dataSelector, queryAllData } from './naming'

let guarding = false

// After the submit event: a disabled button leaves its name and value out of the data.
export function guardFormResubmit() {
  if (guarding) return

  guarding = true

  document.addEventListener('submit', (event) => {
    const form = event.target

    setTimeout(() => {
      if (event.defaultPrevented || !(form instanceof HTMLFormElement)) return

      const target = event.submitter?.getAttribute('formtarget') || form.getAttribute('target')
      if ((target && target !== '_self') || form.hasAttribute('data-allow-resubmit') || event.submitter?.hasAttribute('data-allow-resubmit')) return

      const buttons = [...form.querySelectorAll(`button[type=submit]${dataSelector('button-loading')}:not([disabled])`)]

      for (const button of buttons) {
        button.disabled = true
        button.setAttribute(dataKey('submitting'), '')
      }

      setTimeout(() => {
        for (const button of buttons) {
          if (!button.hasAttribute(dataKey('submitting'))) continue

          button.disabled = false
          button.removeAttribute(dataKey('submitting'))
        }
      }, 10000)
    })
  })

  window.addEventListener('pageshow', (event) => {
    if (!event.persisted) return

    for (const button of queryAllData(document, 'submitting')) {
      button.disabled = false
      button.removeAttribute(dataKey('submitting'))
    }
  })
}

let guardingLoading = false

// Stopped before Livewire's listener: Enter or Space would run the action again while it loads.
export function guardLoadingButtons() {
  if (guardingLoading) return

  guardingLoading = true

  document.addEventListener('click', (event) => {
    // A disabled link-button takes the pointer (its tooltip shows): its clicks are stopped here.
    if (event.target?.closest?.(`${dataSelector('button')}[aria-disabled=true]`)) {
      event.preventDefault()
      event.stopImmediatePropagation()
      return
    }

    const button = event.target?.closest?.(`${dataSelector('button')}${dataSelector('button-loading')}`)

    if (!button || !button.hasAttribute('wire:loading.attr')) return

    event.preventDefault()
    event.stopImmediatePropagation()
  }, true)
}

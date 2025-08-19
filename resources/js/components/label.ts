import { bind } from "../utils"

export function label() {
  return {
    init() {
      if (
        this.$el.tagName.toLowerCase() === 'label' &&
        this.$el.hasAttribute('for') &&
        !!document.getElementById(this.$el.getAttribute('for'))
      ) {
        return
      }

      let control = this.$el.parentElement?.querySelector('[data-tallkit-control]')
      const validSelectors = 'input, select, textarea, [contenteditable=""], [contenteditable="true"], [role="textbox"]'

      if (control && !control.matches(validSelectors)) {
        control = control.querySelector(validSelectors)
      }

      if (!control) {
        return
      }

      bind(this.$el, {
        ['@click']() {
          const tag = control.tagName.toLowerCase()
          const type = control.getAttribute('type')?.toLowerCase()
          const isEditable = control.hasAttribute('contenteditable') || control.getAttribute('role') === 'textbox'
          const isReadOnly = control.hasAttribute('readonly') || control.getAttribute('aria-readonly') === 'true'
          const isDisabled = control.disabled

          if (type === 'checkbox') {
            if (!isDisabled && !isReadOnly) {
              control.checked = !control.checked
              control.dispatchEvent(new Event('input', { bubbles: true }))
              control.dispatchEvent(new Event('change', { bubbles: true }))
            }
            return
          }

          if (type === 'radio') {
            if (!isDisabled && !isReadOnly && !control.checked) {
              control.checked = true
              control.dispatchEvent(new Event('input', { bubbles: true }))
              control.dispatchEvent(new Event('change', { bubbles: true }))
            }
            return
          }

          if ((isEditable || ['input', 'select', 'textarea'].includes(tag)) && typeof control.focus === 'function' && !isDisabled) {
            control.focus()
          }
        }
      })
    }
  }
}

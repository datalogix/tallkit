import { dataKey, bind, setFieldChecked } from '../utils'

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

      let control = this.$el.parentElement
        ?.closest(dataKey('field'))
        ?.querySelector(dataKey('control'))

      if (control && !control.matches('input, select, textarea, [contenteditable=""], [contenteditable="true"], [role="textbox"]')) {
        control = control.querySelector('input, select, textarea, [contenteditable=""], [contenteditable="true"], [role="textbox"]')
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
              setFieldChecked(control, !control.checked)
            }
            return
          }

          if (type === 'radio') {
            if (!isDisabled && !isReadOnly && !control.checked) {
              setFieldChecked(control, true)
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

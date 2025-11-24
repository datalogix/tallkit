export function easymde(options = {}) {
  return {
    easymde: null,

    init () {
      const editor = this.$el.firstElementChild

      this.easymde = new EasyMDE({
        element: editor,
        spellChecker: false,
        ...options
      })

      this.$nextTick(() => this.easymde.value(editor.value))

      this.easymde.codemirror.on('change', () => {
        editor.value = this.easymde.value()
        editor.dispatchEvent(new Event('input', { bubbles: true }))
      })
    }
  }
}

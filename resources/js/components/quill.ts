export function quill(options = {}) {
  return {
    quill: null,

    init () {
      const editor = this.$el.nextElementSibling

      this.quill = new Quill(this.$el, {
          theme: 'snow',
          ...options
      })

      this.$nextTick(() => {
          this.quill.root.innerHTML = editor.value
      })

      this.quill.on('text-change', () => {
          editor.value = this.quill.root.innerHTML
          editor.dispatchEvent(new Event('input', { bubbles: true }))
      });
    }
  }
}

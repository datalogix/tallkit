import { loadRemoteAssets } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { EDITOR_GROUP_ORDER, editorField, parseMode } from '../mixins/editor'
import { loadable } from './loadable'

const GROUPS = {
  text: [
    ['bold', 'italic', 'underline', 'strike'],
  ],
  heading: [
    [{ header: [1, 2, 3, 4, 5, 6, false] }],
  ],
  color: [
    [{ color: [] }, { background: [] }],
  ],
  size: [
    [{ size: ['small', false, 'large', 'huge'] }],
  ],
  script: [
    [{ script: 'sub' }, { script: 'super' }],
  ],
  align: [
    [{ align: [] }],
    [{ indent: '-1' }, { indent: '+1' }],
    [{ direction: 'rtl' }],
  ],
  link: [
    ['link', 'formula'],
  ],
  list: [
    [{ list: 'ordered' }, { list: 'bullet' }, { list: 'check' }],
  ],
  media: [
    ['image', 'video'],
  ],
  quote: [
    ['blockquote'],
  ],
  code: [
    ['code-block'],
  ],
}

function resolveToolbar(mode) {
  const groups = parseMode(mode, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER

  if (!groups.length) {
    return false
  }

  return [...groups.flatMap((group) => GROUPS[group] ?? []), ['clean']]
}

export function quill({ options = {}, scripts = [], styles = [], mode = null } = {}) {
  const _loadable = loadable()

  return {
    ..._loadable,
    ...dataOptions(),
    ...editorField(),

    editor: null,

    init() {
      this.initField()

      this.load(() => loadRemoteAssets(
        () => !!window.Quill && !!window.DOMPurify,
        [
          'https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js',
          'https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js',
          ...scripts
        ],
        ['https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css', ...styles]
      ).then(() => this.mount()))
    },

    applyExternalValue(value) {
      this.editor.clipboard.dangerouslyPasteHTML(window.DOMPurify.sanitize(value ?? ''))
    },

    mount() {
      try {
        this.editor = new window.Quill(this.$refs.root, {
          theme: 'snow',
          modules: {
            toolbar: resolveToolbar(mode)
          },
          ...options,
          ...this.getDataOptions(this.$refs.root)
        })

        this.editor.on('text-change', () => {
          this.sync(this.editor.root.innerHTML)
        })

        this.$dispatch('rendered', { editor: this.editor })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.editor?.off('text-change')
      this.editor = null
    }
  };
}

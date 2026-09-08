import { loadRemoteAssets } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { EDITOR_GROUP_ORDER, editorField, parseMode } from '../mixins/editor'
import { loadable } from './loadable'

const GROUPS = {
  text: {
    toolbar: 'bold italic underline strikethrough | removeformat',
  },
  heading: {
    toolbar: 'blocks',
  },
  color: {
    toolbar: 'forecolor backcolor',
  },
  size: {
    toolbar: 'fontsize',
  },
  script: {
    toolbar: 'subscript superscript',
  },
  align: {
    toolbar: 'alignleft aligncenter alignright alignjustify',
  },
  link: {
    plugins: 'link autolink',
    toolbar: 'link',
  },
  list: {
    plugins: 'lists',
    toolbar: 'numlist bullist',
  },
  media: {
    plugins: 'image media',
    toolbar: 'image media',
  },
  table: {
    plugins: 'table',
    toolbar: 'table',
  },
  quote: {
    toolbar: 'blockquote',
  },
  code: {
    plugins: 'code codesample',
    toolbar: 'code codesample',
  },
}

function resolveConfig(mode) {
  const groups = parseMode(mode, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER

  if (!groups.length) {
    return { plugins: '', toolbar: false };
  }

  return {
    plugins: groups.map((group) => GROUPS[group]?.plugins).filter(Boolean).join(' '),
    toolbar: ['undo redo', ...groups.map((group) => GROUPS[group]?.toolbar).filter(Boolean)].join(' | '),
  }
}

export function tinymce({ options = {}, scripts = [], mode = null } = {}) {
  const _loadable = loadable()

  return {
    ..._loadable,
    ...dataOptions(),
    ...editorField(),

    editor: null,

    init() {
      this.initField()

      this.load(() => loadRemoteAssets(
        () => !!window.tinymce,
        ['https://cdn.jsdelivr.net/npm/tinymce@8/tinymce.min.js', ...scripts]
      ).then(() => this.mount()))
    },

    applyExternalValue(value) {
      this.editor.setContent(value ?? '')
    },

    async mount() {
      try {
        const { plugins, toolbar } = resolveConfig(mode)

        const [editor] = await window.tinymce.init({
          target: this.input,
          license_key: 'gpl',
          menubar: false,
          plugins,
          toolbar,
          promotion: false,
          branding: false,
          setup: (editor) => {
            editor.on('change input undo redo', () => {
              this.sync(editor.getContent())
            })
          },
          ...options,
          ...this.getDataOptions(this.input),
        })

        this.editor = editor
        this.$dispatch('rendered', { editor: this.editor })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.editor?.remove()
      this.editor = null
    }
  };
}

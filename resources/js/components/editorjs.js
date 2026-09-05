import { loadRemoteAssets } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { EDITOR_GROUP_ORDER, editorField, parseMode } from '../mixins/editor'
import { loadable } from './loadable'

const GROUPS = {
  text: {
    scripts: [
      'https://cdn.jsdelivr.net/npm/@editorjs/inline-code@1',
      'https://cdn.jsdelivr.net/npm/@editorjs/underline@1',
    ],
    inline: ['bold', 'italic', 'underline', 'inlineCode'],
    tools: () => ({
      inlineCode: window.InlineCode,
      underline: window.Underline,
    }),
  },
  heading: {
    scripts: ['https://cdn.jsdelivr.net/npm/@editorjs/header@2'],
    tools: () => ({
      heading: window.Header,
    }),
  },
  color: {
    scripts: ['https://cdn.jsdelivr.net/npm/@editorjs/marker@1'],
    inline: ['marker'],
    tools: () => ({
      marker: window.Marker,
    }),
  },
  link: {
    scripts: [],
    inline: ['link'],
    tools: () => ({}),
  },
  list: {
    scripts: ['https://cdn.jsdelivr.net/npm/@editorjs/list@2'],
    tools: () => ({
      list: { class: window.EditorjsList, inlineToolbar: true },
    }),
  },
  media: {
    scripts: [
      'https://cdn.jsdelivr.net/npm/@editorjs/simple-image@1',
      'https://cdn.jsdelivr.net/npm/@editorjs/embed@2',
    ],
    tools: () => ({
      simpleImage: window.SimpleImage,
      embed: window.Embed,
    }),
  },
  table: {
    scripts: ['https://cdn.jsdelivr.net/npm/@editorjs/table@2'],
    tools: () => ({
      table: window.Table,
    }),
  },
  quote: {
    scripts: [
      'https://cdn.jsdelivr.net/npm/@editorjs/quote@2',
      'https://cdn.jsdelivr.net/npm/@editorjs/warning@1',
      'https://cdn.jsdelivr.net/npm/@editorjs/delimiter@1',
    ],
    tools: () => ({
      quote: { class: window.Quote, inlineToolbar: true },
      warning: window.Warning,
      delimiter: window.Delimiter,
    }),
  },
  code: {
    scripts: [
      'https://cdn.jsdelivr.net/npm/@editorjs/code@2',
      'https://cdn.jsdelivr.net/npm/@editorjs/raw@2',
    ],
    tools: () => ({
      code: window.CodeTool,
      raw: window.RawTool,
    }),
  },
}

export function editorjs({ options = {}, scripts = [], styles = [], mode = null } = {}) {
  const _loadable = loadable()

  return {
    ..._loadable,
    ...dataOptions(),
    ...editorField(),

    editor: null,
    _saveToken: 0,

    init() {
      this.initField()

      const groups = parseMode(mode, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER

      this.load(() => loadRemoteAssets(() => !!window.EditorJS, [
        'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2',
        ...groups.flatMap((group) => GROUPS[group]?.scripts ?? []),
        ...scripts,
      ], styles).then(() => this.mount(groups)))
    },

    applyExternalValue(value) {
      this.editor.render(value ? JSON.parse(value) : { blocks: [] })
    },

    mount(groups) {
      try {
        this.editor = new window.EditorJS({
          holder: this.$refs.root,
          tools: groups.reduce((tools, group) => ({ ...tools, ...GROUPS[group]?.tools() }), {}),
          inlineToolbar: groups.flatMap((group) => GROUPS[group]?.inline ?? []),
          data: this.input.value ? JSON.parse(this.input.value) : undefined,
          onChange: async (api) => {
            const token = ++this._saveToken
            const output = await api.saver.save()

            if (token !== this._saveToken) return

            this.sync(JSON.stringify(output))
          },
          ...options,
          ...this.getDataOptions(),
        })

        this.editor.isReady.then(() => this.$dispatch('rendered', { editor: this.editor }))
      } catch (e) {
        this.fail(e)
      }
    },

    async destroy() {
      _loadable.destroy.call(this)
      await this.editor?.destroy()
      this.editor = null
    }
  };
}

import { loadRemoteAssets, emit } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { EDITOR_GROUP_ORDER, editorField, parseToolbar } from '../mixins/editor'
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

function parseData(value) {
  if (!value) return undefined

  try {
    const data = typeof value === 'string' ? JSON.parse(value) : value

    if (data && Array.isArray(data.blocks)) return data
  } catch {
  }

  console.warn('[tallkit] The Editor.js value is not Editor.js data (JSON with "blocks"): it starts empty.', value)

  return undefined
}

export function editorjs({ options = {}, scripts = [], styles = [], toolbar = null, i18n = null } = {}) {
  const _loadable = loadable()

  // Out of Alpine's reactive data: called through its proxy, the editor breaks.
  let editor = null

  return {
    ..._loadable,
    ...dataOptions(),
    ...editorField(),

    _saveToken: 0,

    getEditor() {
      return editor
    },

    init() {
      this.initField()

      const groups = parseToolbar(toolbar, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER

      this.load(() => loadRemoteAssets(() => !!window.EditorJS, [
        'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2',
        ...groups.flatMap((group) => GROUPS[group]?.scripts ?? []),
        ...scripts,
      ], styles).then(() => this.mount(groups)))
    },

    applyExternalValue(value) {
      editor.render(parseData(value) ?? { blocks: [] })
    },

    mount(groups) {
      if (this.isDestroyed()) return

      // Not caught here: load() shows it, and its completion would hide it.
      editor = new window.EditorJS({
        holder: this.$refs.root,
        tools: groups.reduce((tools, group) => ({ ...tools, ...GROUPS[group]?.tools() }), {}),
        inlineToolbar: groups.flatMap((group) => GROUPS[group]?.inline ?? []),
        ...(i18n ? { i18n: { messages: i18n } } : {}),
        data: parseData(this.input.value),
        readOnly: this.lockState() !== null,
        onChange: async (api) => {
          const token = ++this._saveToken
          const output = await api.saver.save()

          if (token !== this._saveToken) return

          this.sync(output.blocks?.length ? JSON.stringify(output) : '')
        },
        ...options,
        ...this.getDataOptions(this.$refs.root),
      })

      return editor.isReady.then(() => {
        const instance = editor

        this.followLockState((locked) => {
          if (instance?.readOnly && instance.readOnly.isEnabled !== locked) instance.readOnly.toggle(locked)
        })

        emit(this.input, 'rendered', { editor }, { later: true })
      })
    },

    async destroy() {
      _loadable.destroy.call(this)
      this.stopFollowingLockState()
      const instance = editor
      editor = null
      await instance?.destroy()
    }
  }
}

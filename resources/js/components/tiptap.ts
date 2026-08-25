import { loadRemoteModule } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { EDITOR_GROUP_ORDER, editorField, parseMode, warnUnsupportedGroups } from '../mixins/editor'
import { loadable } from './loadable'

const TIPTAP_VERSION = '3.30.3'
const esm = (pkg: string) => `https://esm.sh/${pkg}@${TIPTAP_VERSION}`

const GROUPS: Record<string, { scripts?: string[], extensions?(mods: any[]): any[] }> = {
  text: {},
  heading: {},
  color: {
    scripts: [esm('@tiptap/extension-text-style')],
    extensions: ([textStyle]) => [textStyle.TextStyleKit],
  },
  size: {
    scripts: [esm('@tiptap/extension-text-style')],
    extensions: ([textStyle]) => [textStyle.TextStyleKit],
  },
  script: {
    scripts: [esm('@tiptap/extension-subscript'), esm('@tiptap/extension-superscript')],
    extensions: ([subscript, superscript]) => [subscript.default, superscript.default],
  },
  align: {
    scripts: [esm('@tiptap/extension-text-align')],
    extensions: ([textAlign]) => [textAlign.default.configure({ types: ['heading', 'paragraph'] })],
  },
  link: {},
  list: {},
  media: {
    scripts: [esm('@tiptap/extension-image')],
    extensions: ([image]) => [image.default],
  },
  table: {
    scripts: [esm('@tiptap/extension-table')],
    extensions: ([table]) => [table.TableKit],
  },
  quote: {},
  code: {},
}

export function tiptap({ options = {}, scripts = [], mode = null } = {}) {
  const _loadable = loadable()

  let editor: any = null

  return {
    ..._loadable,
    ...dataOptions(),
    ...editorField(),

    groups: [],
    extraModules: [],
    tick: 0,

    init() {
      this.initField()

      const groups = parseMode(mode, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER
      warnUnsupportedGroups('tiptap', groups, GROUPS)
      this.groups = groups

      this.load(async () => {
        const [{ Editor }, { default: StarterKit }] = await loadRemoteModule([
          esm('@tiptap/core'),
          esm('@tiptap/starter-kit'),
        ])

        const extensions = [StarterKit]

        for (const group of groups) {
          const config = GROUPS[group]

          if (!config?.scripts?.length) continue

          const mods = await loadRemoteModule(config.scripts)

          for (const extension of config.extensions?.(mods) ?? []) {
            if (!extension || extensions.includes(extension)) continue
            extensions.push(extension)
          }
        }

        if (scripts.length) {
          this.extraModules = await loadRemoteModule(scripts)
        }

        this.mount(Editor, extensions)
      })
    },

    applyExternalValue(value) {
      editor.commands.setContent(value ?? '')
    },

    run(command: string) {
      const chain = editor.chain().focus()

      if (command === 'heading1') chain.toggleHeading({ level: 1 })
      else if (command === 'heading2') chain.toggleHeading({ level: 2 })
      else if (command === 'heading3') chain.toggleHeading({ level: 3 })
      else if (command.startsWith('align')) chain.setTextAlign(command.slice(5).toLowerCase())
      else if (command === 'link') {
        const url = window.prompt('URL', editor.getAttributes('link').href ?? '')
        url ? chain.setLink({ href: url }) : chain.unsetLink()
      } else if (command === 'image') {
        const url = window.prompt('Image URL')
        if (url) chain.setImage({ src: url })
      } else if (command === 'table') {
        chain.insertTable({ rows: 3, cols: 3, withHeaderRow: true })
      } else {
        chain[`toggle${command.charAt(0).toUpperCase()}${command.slice(1)}`]?.()
      }

      chain.run()
    },

    isActive(command: string) {
      this.tick

      if (!editor) return false
      if (command === 'heading1') return editor.isActive('heading', { level: 1 })
      if (command === 'heading2') return editor.isActive('heading', { level: 2 })
      if (command === 'heading3') return editor.isActive('heading', { level: 3 })
      if (command.startsWith('align')) return editor.isActive({ textAlign: command.slice(5).toLowerCase() })

      return editor.isActive(command)
    },

    setColor(value: string) {
      value ? editor.chain().focus().setColor(value).run() : editor.chain().focus().unsetColor().run()
    },

    setBackgroundColor(value: string) {
      value ? editor.chain().focus().setBackgroundColor(value).run() : editor.chain().focus().unsetBackgroundColor().run()
    },

    setFontSize(value: string) {
      value ? editor.chain().focus().setFontSize(value).run() : editor.chain().focus().unsetFontSize().run()
    },

    mount(Editor, extensions) {
      try {
        editor = new Editor({
          element: this.$refs.root,
          extensions,
          content: this.input.value ?? '',
          onUpdate: ({ editor }) => {
            this.sync(editor.getHTML())
          },
          onSelectionUpdate: () => { this.tick++ },
          onTransaction: () => { this.tick++ },
          ...options,
          ...this.getDataOptions(),
        })

        this.$dispatch('rendered', { editor })
      } catch (e) {
        this.fail(e)
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      editor?.destroy()
      editor = null
    }
  }
}

import { loadRemoteModule } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { EDITOR_GROUP_ORDER, editorField, parseMode } from '../mixins/editor'
import { loadable } from './loadable'

const DEFAULT_TIPTAP_VERSION = '3.30.3'
const esm = (pkg, version) => `https://esm.sh/${pkg}@${version}`

function readAsDataURL(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = () => resolve(reader.result)
    reader.onerror = () => reject(reader.error)
    reader.readAsDataURL(file)
  });
}

function getCsrfToken() {
  const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/)
  return match ? decodeURIComponent(match[1]) : null
}

const GROUPS = {
  text: {},
  heading: {},
  color: {
    scripts: (v) => [esm('@tiptap/extension-text-style', v)],
    extensions: ([textStyle]) => [textStyle.TextStyleKit],
  },
  size: {
    scripts: (v) => [esm('@tiptap/extension-text-style', v)],
    extensions: ([textStyle]) => [textStyle.TextStyleKit],
  },
  script: {
    scripts: (v) => [esm('@tiptap/extension-subscript', v), esm('@tiptap/extension-superscript', v)],
    extensions: ([subscript, superscript]) => [subscript.default, superscript.default],
  },
  align: {
    scripts: (v) => [esm('@tiptap/extension-text-align', v)],
    extensions: ([textAlign]) => [textAlign.default.configure({ types: ['heading', 'paragraph'] })],
  },
  link: {},
  list: {},
  media: {
    scripts: (v) => [esm('@tiptap/extension-image', v), esm('@tiptap/core', v)],
    extensions: ([image, core]) => [
      image.default.configure({
        resize: {
          enabled: true,
          alwaysPreserveAspectRatio: true,
        },
      }),
      core.Node.create({
        name: 'video',
        group: 'block',
        atom: true,
        draggable: true,

        addAttributes() {
          return {
            src: { default: null },
            width: {
              default: null,
              renderHTML: (attrs) => (attrs.width ? { style: `width: ${attrs.width}` } : {}),
            },
          };
        },

        parseHTML() {
          return [{ tag: 'video' }]
        },

        renderHTML({
          HTMLAttributes
        }) {
          return ['video', core.mergeAttributes({ controls: '' }, HTMLAttributes)]
        },

        addCommands() {
          return {
            setVideo: (options) => ({
              commands
            }) => commands.insertContent({ type: this.name, attrs: options }),
          };
        },
      }),
    ],
  },
  table: {
    scripts: (v) => [esm('@tiptap/extension-table', v)],
    extensions: ([table]) => [table.TableKit],
  },
  quote: {},
  code: {},
}

export function tiptap(
  {
    options = {},
    scripts = [],
    mode = null,
    upload = {},
    version = null
  } = {}
) {
  const _loadable = loadable()

  let resolvedVersion = version || DEFAULT_TIPTAP_VERSION

  let editor = null

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
      this.groups = groups

      this.load(async () => {
        const [{ Editor }, { default: StarterKit }] = await loadRemoteModule([
          esm('@tiptap/core', resolvedVersion),
          esm('@tiptap/starter-kit', resolvedVersion),
        ])

        const extensions = [StarterKit]

        for (const group of groups) {
          const config = GROUPS[group]
          const groupScripts = config?.scripts?.(resolvedVersion)

          if (!groupScripts?.length) continue

          const mods = await loadRemoteModule(groupScripts)

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

    run(command) {
      const chain = editor.chain().focus()

      if (command === 'heading1') chain.toggleHeading({ level: 1 })
      else if (command === 'heading2') chain.toggleHeading({ level: 2 })
      else if (command === 'heading3') chain.toggleHeading({ level: 3 })
      else if (command.startsWith('align')) chain.setTextAlign(command.slice(5).toLowerCase())
      else if (command === 'link') {
        const url = window.prompt('URL', editor.getAttributes('link').href ?? '')
        url ? chain.setLink({ href: url }) : chain.unsetLink()
      } else if (command === 'image') {
        this.$refs.imageInput?.click()
      } else if (command === 'video') {
        this.$refs.videoInput?.click()
      } else if (command === 'table') {
        chain.insertTable({ rows: 3, cols: 3, withHeaderRow: true })
      } else {
        chain[`toggle${command.charAt(0).toUpperCase()}${command.slice(1)}`]?.()
      }

      chain.run()
    },

    async handleUpload(file, type) {
      if (!upload) {
        return readAsDataURL(file)
      }

      const limit = upload.maxSize?.[type]

      if (limit && file.size > limit * 1024) {
        throw new Error(`File is larger than the ${(limit / 1024).toFixed(1)}MB limit.`)
      }

      const body = new FormData()
      body.append('file', file)
      if (limit) body.append('max_size', String(limit))
      if (upload.disk) body.append('disk', upload.disk)
      if (upload.directory) body.append('directory', upload.directory)

      const response = await fetch(upload.url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          Accept: 'application/json',
          'X-XSRF-TOKEN': getCsrfToken() ?? '',
        },
        body,
      })

      if (!response.ok) {
        throw new Error(`Upload failed with status ${response.status}`)
      }

      return (await response.json()).url
    },

    async insertImage(event) {
      const input = event.target
      const file = input.files?.[0]
      input.value = ''

      if (!file) return

      try {
        const src = await this.handleUpload(file, 'image')
        editor.chain().focus().setImage({ src, alt: file.name }).run()
      } catch (e) {
        console.error(e)
        window.alert(e instanceof Error ? e.message : 'Failed to upload image.')
      }
    },

    async insertVideo(event) {
      const input = event.target
      const file = input.files?.[0]
      input.value = ''

      if (!file) return

      try {
        const src = await this.handleUpload(file, 'video')
        editor.chain().focus().setVideo({ src }).run()
      } catch (e) {
        console.error(e)
        window.alert(e instanceof Error ? e.message : 'Failed to upload video.')
      }
    },

    textStyle(attr) {
      this.tick

      return editor?.getAttributes('textStyle')[attr] ?? null
    },

    isActive(command) {
      this.tick

      if (!editor) return false
      if (command === 'heading1') return editor.isActive('heading', { level: 1 })
      if (command === 'heading2') return editor.isActive('heading', { level: 2 })
      if (command === 'heading3') return editor.isActive('heading', { level: 3 })
      if (command.startsWith('align')) return editor.isActive({ textAlign: command.slice(5).toLowerCase() })

      return editor.isActive(command)
    },

    setColor(value) {
      value ? editor.chain().focus().setColor(value).run() : editor.chain().focus().unsetColor().run()
    },

    setBackgroundColor(value) {
      value ? editor.chain().focus().setBackgroundColor(value).run() : editor.chain().focus().unsetBackgroundColor().run()
    },

    setFontSize(value) {
      value ? editor.chain().focus().setFontSize(value).run() : editor.chain().focus().unsetFontSize().run()
    },

    mount(EditorClass, extensions) {
      try {
        editor = new EditorClass({
          element: this.$refs.root,
          extensions,
          content: this.input.value ?? '',
          editorProps: {
            attributes: {
              class: 'tiptap-content',
              'data-tallkit-control': '',
            },
          },
          onUpdate: ({
            editor
          }) => { this.sync(editor.getHTML()) },
          onSelectionUpdate: () => { this.tick++ },
          onTransaction: () => { this.tick++ },
          ...options,
          ...this.getDataOptions(this.$refs.root),
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
  };
}

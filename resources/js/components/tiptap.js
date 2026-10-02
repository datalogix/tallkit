import { dataKey, queryData, loadRemoteModule, uploadEditorFile, reportUploadFailed, emit, isRtl, isRendered } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { EDITOR_GROUP_ORDER, editorField, parseToolbar } from '../mixins/editor'
import { loadable } from './loadable'

const DEFAULT_TIPTAP_VERSION = '3.30.3'
// Pinned to one version: two copies of @tiptap/core on a page break it.
const esm = (pkg, version) => {
  const deps = ['@tiptap/core', '@tiptap/pm'].filter((dep) => dep !== pkg).map((dep) => `${dep}@${version}`)

  return `https://esm.sh/${pkg}@${version}?deps=${deps.join(',')}`
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
          }
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
          }
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
    toolbar = null,
    upload = {},
    version = null,
    messages = {},
    labelledBy = null,
  } = {}
) {
  const _loadable = loadable()

  let resolvedVersion = version || DEFAULT_TIPTAP_VERSION

  let editor = null

  const message = (key, replace = {}) => Object.entries(replace).reduce(
    (text, [name, value]) => text.replaceAll(`:${name}`, value),
    messages[key] ?? {
      linkUrl: 'Link URL',
    }[key],
  )

  return {
    ..._loadable,
    ...dataOptions(),
    ...editorField(),

    groups: [],
    extraModules: [],

    // The editor isn't reactive: tick makes Alpine read its state again.
    tick: 0,

    // Out of Alpine's reactive data: called through its proxy, the editor breaks.
    getEditor() {
      return editor
    },

    init() {
      this.initField()

      const groups = parseToolbar(toolbar, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER
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
        const url = window.prompt(message('linkUrl'), editor.getAttributes('link').href ?? '')

        if (url === null) return

        url.trim() ? chain.setLink({ href: url.trim() }) : chain.unsetLink()
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

    handleUpload(file, type) {
      return uploadEditorFile(file, type, upload, messages)
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
        this.uploadFailed(e, file, 'image')
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
        this.uploadFailed(e, file, 'video')
      }
    },

    uploadFailed(error, file, type) {
      reportUploadFailed(this.input ?? this.$root, error, file, type, messages)
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
      if (this.isDestroyed()) return

      // Not caught here: load() shows it, and its completion would hide it.
      editor = new EditorClass({
        element: this.$refs.root,
        extensions,
        content: this.input.value ?? '',
        editorProps: {
          attributes: {
            class: 'tiptap-content',
            [dataKey('control')]: '',
            // Given here: Tiptap only adds its own on the first draw, and setEditable() draws these again.
            role: 'textbox',
            'aria-multiline': 'true',
            ...(labelledBy ? { 'aria-labelledby': labelledBy } : {}),
          },
        },
        // An empty editor holds "<p></p>": sent as nothing, or a required field counts as filled.
        onUpdate: ({
          editor
        }) => { this.sync(editor.isEmpty ? '' : editor.getHTML()) },
        onSelectionUpdate: () => { this.tick++ },
        onTransaction: () => { this.tick++ },
        ...options,
        ...this.getDataOptions(this.$refs.root),
      })

      this.followLockState((locked) => {
        if (editor && editor.isEditable === locked) editor.setEditable(!locked)
      }, () => queryData(this.$root, 'editor-toolbar'))

      this.initToolbarKeys()

      emit(this.input, 'rendered', { editor }, { later: true })
    },

    pressedState(command) {
      if (['link', 'image', 'video', 'table'].includes(command)) return null

      return this.isActive(command) ? 'true' : 'false'
    },

    initToolbarKeys() {
      const toolbar = queryData(this.$root, 'editor-toolbar')
      if (!toolbar) return

      const items = () => Array.from(toolbar.querySelectorAll('button'))
        .filter((el) => !el.closest('[popover]') && !el.disabled && isRendered(el))

      const makeCurrent = (current) => {
        items().forEach((el) => el.setAttribute('tabindex', el === current ? '0' : '-1'))
      }

      makeCurrent(items()[0])

      toolbar.addEventListener('focusin', (event) => {
        if (items().includes(event.target)) makeCurrent(event.target)
      })

      toolbar.addEventListener('keydown', (event) => {
        const list = items()
        const index = list.indexOf(event.target)

        if (index === -1) return

        const rtl = isRtl(toolbar)
        const step = { ArrowRight: rtl ? -1 : 1, ArrowLeft: rtl ? 1 : -1 }[event.key]
        let next = null

        if (step) next = list[(index + step + list.length) % list.length]
        else if (event.key === 'Home') next = list[0]
        else if (event.key === 'End') next = list.at(-1)

        if (!next) return

        event.preventDefault()
        makeCurrent(next)
        next.focus()
      })
    },

    destroy() {
      _loadable.destroy.call(this)
      this.stopFollowingLockState()
      editor?.destroy()
      editor = null
    }
  }
}

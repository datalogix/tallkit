import { dataKey, dataSelector, loadRemoteAssets, uploadEditorFile, reportUploadFailed, emit } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { EDITOR_GROUP_ORDER, editorField, parseToolbar } from '../mixins/editor'
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
    ['link'],
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

function resolveToolbar(toolbar) {
  const groups = parseToolbar(toolbar, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER

  if (!groups.length) {
    return false
  }

  return [...groups.flatMap((group) => GROUPS[group] ?? []), ['clean']]
}

function translateStylesheet(texts = {}) {
  if (!Object.keys(texts).length || document.querySelector(`style${dataSelector('quill-i18n')}`)) return

  const q = (text) => JSON.stringify(String(text ?? ''))
  const picker = (name, value) => value === null
    ? `.ql-snow .ql-picker.ql-${name} .ql-picker-label::before, .ql-snow .ql-picker.ql-${name} .ql-picker-item::before`
    : `.ql-snow .ql-picker.ql-${name} .ql-picker-label[data-value="${value}"]::before, .ql-snow .ql-picker.ql-${name} .ql-picker-item[data-value="${value}"]::before`

  const rules = [
    [picker('header', null), texts.normal],
    ...[1, 2, 3, 4, 5, 6].map((level) => [picker('header', level), texts[`heading${level}`]]),
    [picker('size', null), texts.normal],
    [picker('size', 'small'), texts.small],
    [picker('size', 'large'), texts.large],
    [picker('size', 'huge'), texts.huge],
    ['.ql-snow .ql-tooltip::before', texts.visit],
    ['.ql-snow .ql-tooltip a.ql-action::after', texts.edit],
    ['.ql-snow .ql-tooltip a.ql-remove::before', texts.remove],
    ['.ql-snow .ql-tooltip.ql-editing a.ql-action::after', texts.save],
    ['.ql-snow .ql-tooltip[data-mode=link]::before', texts.enterLink],
    ['.ql-snow .ql-tooltip[data-mode=video]::before', texts.enterVideo],
    ['.ql-snow .ql-tooltip[data-mode=formula]::before', texts.enterFormula],
  ].filter(([, text]) => text)

  const style = document.createElement('style')
  style.setAttribute(dataKey('quill-i18n'), '')
  style.textContent = rules.map(([selector, text]) => `${selector} { content: ${q(text)}; }`).join('\n')
  document.head.appendChild(style)
}

export function quill({ options = {}, scripts = [], styles = [], toolbar = null, upload = null, messages = {}, labelledBy = null, i18n = {} } = {}) {
  const _loadable = loadable()

  // Out of Alpine's reactive data: called through its proxy, Quill spreads a format over the text after it.
  let editor = null

  return {
    ..._loadable,
    ...dataOptions(),
    ...editorField(),

    getEditor() {
      return editor
    },

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
      this.setHtml(value)
    },

    // Silent: it is the field's value already.
    setHtml(value) {
      editor.setContents(editor.clipboard.convert({ html: window.DOMPurify.sanitize(value ?? '') }), 'silent')
    },

    // An empty editor holds "<p><br></p>": sent as nothing, or a required field counts as filled.
    html() {
      return editor.getLength() <= 1 ? '' : editor.root.innerHTML
    },

    mount() {
      if (this.isDestroyed()) return

      // Not caught here: load() shows it, and its completion would hide it.
      const { modules = {}, ...rest } = options

      editor = new window.Quill(this.$refs.root, {
        theme: 'snow',
        ...rest,
        modules: {
          toolbar: resolveToolbar(toolbar),
          uploader: {
            mimetypes: ['image/png', 'image/jpeg', 'image/gif', 'image/webp'],
            handler: (range, files) => this.uploadImages(range, files),
          },
          ...modules,
          // Tab leaves the editor instead of typing a tab (a keyboard trap otherwise); in a list it still indents.
          keyboard: {
            ...(modules.keyboard ?? {}),
            bindings: {
              tab: { key: 'Tab', handler: () => true },
              ...(modules.keyboard?.bindings ?? {}),
            },
          },
        },
        ...this.getDataOptions(this.$refs.root)
      })

      editor.root.setAttribute('role', 'textbox')
      editor.root.setAttribute('aria-multiline', 'true')
      if (labelledBy) editor.root.setAttribute('aria-labelledby', labelledBy)

      // Started from the saved value, or the first key typed would replace it.
      if (this.input.value) {
        this.setHtml(this.input.value)
      }

      editor.on('text-change', () => {
        this.sync(this.html())
      })

      this.followLockState((locked) => editor?.enable(!locked), () => editor?.getModule('toolbar')?.container)

      const buttons = i18n.buttons ?? {}
      const toolbarEl = editor.getModule('toolbar')?.container

      toolbarEl?.querySelectorAll('button[class*="ql-"], .ql-picker').forEach((control) => {
        const format = Array.from(control.classList).find((name) => name.startsWith('ql-') && name !== 'ql-picker')?.slice(3)
        const label = buttons[control.value ? `${format}:${control.value}` : format] ?? buttons[format]

        if (!label) return

        const target = control.matches('.ql-picker') ? control.querySelector('.ql-picker-label') : control

        target?.setAttribute('aria-label', label)
        target?.setAttribute('title', label)
      })

      translateStylesheet(i18n.texts)

      emit(this.input, 'rendered', { editor }, { later: true })
    },

    async uploadImages(range, files) {
      let index = range?.index ?? editor.getLength()

      for (const file of files) {
        try {
          const url = await uploadEditorFile(file, 'image', upload, messages)

          if (!editor) return

          editor.insertEmbed(index, 'image', url, 'user')
          editor.setSelection(++index, 0, 'silent')
        } catch (e) {
          reportUploadFailed(this.input ?? this.$root, e, file, 'image', messages)
        }
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.stopFollowingLockState()
      editor?.off('text-change')
      editor = null
    }
  }
}

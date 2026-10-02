import { loadRemoteAssets, loadScript, uploadEditorFile, reportUploadFailed, emit } from '../utils'
import { dataOptions } from '../mixins/data-options'
import { EDITOR_GROUP_ORDER, editorField, parseToolbar } from '../mixins/editor'
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

const isDark = () => document.documentElement.classList.contains('dark')

function resolveConfig(toolbar) {
  const groups = parseToolbar(toolbar, EDITOR_GROUP_ORDER) ?? EDITOR_GROUP_ORDER

  if (!groups.length) {
    return { plugins: '', toolbar: false }
  }

  return {
    plugins: groups.map((group) => GROUPS[group]?.plugins).filter(Boolean).join(' '),
    toolbar: ['undo redo', ...groups.map((group) => GROUPS[group]?.toolbar).filter(Boolean)].join(' | '),
  }
}

const LANGUAGES = 'https://cdn.jsdelivr.net/npm/tinymce-i18n@26/langs8'

async function loadLanguage(locale) {
  if (!locale) return null

  const [language, region] = String(locale).replace('_', '-').split('-')

  if (language.toLowerCase() === 'en') return null

  const candidates = [...new Set([
    region ? `${language.toLowerCase()}-${region.toUpperCase()}` : null,
    language.toLowerCase(),
    `${language.toLowerCase()}-${language.toUpperCase()}`,
  ].filter(Boolean))]

  for (const name of candidates) {
    try {
      await loadScript(`${LANGUAGES}/${name}.js`)

      return name
    } catch {
    }
  }

  return null
}

export function tinymce({ options = {}, scripts = [], toolbar = null, upload = null, messages = {}, title = null, locale = null } = {}) {
  const _loadable = loadable()

  // Out of Alpine's reactive data: called through its proxy, the editor breaks.
  let editor = null

  return {
    ..._loadable,
    ...dataOptions(),
    ...editorField(),

    _appearanceObserver: null,

    getEditor() {
      return editor
    },

    init() {
      this.initField()

      this.load(() => loadRemoteAssets(
        () => !!window.tinymce,
        ['https://cdn.jsdelivr.net/npm/tinymce@8/tinymce.min.js', ...scripts]
      ).then(() => this.mount()))

      let dark = isDark()

      this._appearanceObserver = new MutationObserver(() => {
        if (isDark() === dark) return
        dark = isDark()

        if (!editor || this.isDestroyed()) return

        editor.remove()
        editor = null
        this.mount().catch((e) => this.fail(e))
      })

      this._appearanceObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })
    },

    applyExternalValue(value) {
      editor.setContent(value ?? '')
    },

    async mount() {
      if (this.isDestroyed()) return

      // Not caught here: load() shows it, and its completion would hide it.
      const { plugins, toolbar: buttons } = resolveConfig(toolbar)
      const dark = isDark()

      const language = await loadLanguage(locale)

      const [created] = await window.tinymce.init({
        ...(language ? { language } : {}),
        target: this.input,
        license_key: 'gpl',
        menubar: false,
        plugins,
        toolbar: buttons,
        promotion: false,
        branding: false,
        skin: dark ? 'oxide-dark' : 'oxide',
        content_css: dark ? 'dark' : 'default',
        ...(upload?.url ? { images_upload_handler: (blobInfo) => this.uploadImage(blobInfo) } : {}),
        // Absolute addresses: a relative one breaks wherever the content is shown at another path.
        convert_urls: false,
        ...(title ? { iframe_aria_text: title } : {}),
        setup: (instance) => {
          instance.on('change input undo redo', () => {
            this.sync(instance.getContent())
          })
        },
        ...options,
        ...this.getDataOptions(this.input),
      })

      if (this.isDestroyed()) {
        created?.remove()
        return
      }

      editor = created

      // iframe_aria_text names the text, not the iframe: its title is set here.
      editor.iframeElement?.setAttribute('title', title || editor.options?.get?.('iframe_aria_text') || 'Rich Text Area')

      this.followLockState((locked) => editor?.mode.set(locked ? 'readonly' : 'design'))

      emit(this.input, 'rendered', { editor }, { later: true })
    },

    async uploadImage(blobInfo) {
      const blob = blobInfo.blob()
      const file = new File([blob], blobInfo.filename(), { type: blob.type })

      try {
        return await uploadEditorFile(file, 'image', upload, messages)
      } catch (e) {
        throw { message: reportUploadFailed(this.input ?? this.$root, e, file, 'image', messages, false), remove: true }
      }
    },

    destroy() {
      _loadable.destroy.call(this)
      this.stopFollowingLockState()
      this._appearanceObserver?.disconnect()
      editor?.remove()
      editor = null
    }
  }
}

import { dataKey, bind, formatBytes, detectFileType, generateId, findInField } from '../utils'

const PREVIEWABLE_TYPES = ['image', 'video', 'audio', 'pdf']

export function upload(
  {
    wireModel = false,
    multiple = false,
    droppable = true,
    maxSize = null,
    maxFiles = null,
    sortable = false,
    invalid = false,
    files = [],
    tooLargeMessage = 'This file is too large.',
    invalidTypeMessage = 'This file type is not allowed.',
    tooManyFilesMessage = 'Too many files selected.',
    previewName = null
  } = {}
) {
  return {
    dragOver: false,
    dragIndex: null,
    dragOverIndex: null,
    sortable,
    previewId: null,

    files: files.map(file => ({
      id: file.id ?? generateId('upload-file'),
      raw: null,
      name: file.name ?? '',
      size: file.size ?? 0,
      url: file.url ?? null,
      value: file.value ?? null,
      type: file.type ?? 'unknown',
      status: file.status ?? 'done',
      progress: file.progress ?? 100,
      error: null,
      tmpFilename: file.tmpFilename ?? null,
      previewLoaded: PREVIEWABLE_TYPES.includes(file.type ?? '')
    })),

    queue: [],
    activeId: null,

    multiple() {
      return (this.$refs.fileInput)?.multiple ?? multiple;
    },

    accept() {
      return (this.$refs.fileInput)?.accept || null;
    },

    activeFiles() {
      return this.files.filter((file) => file.status === 'uploading' || file.status === 'queued');
    },

    hasPendingUploads() {
      return this.files.some((file) =>
        file.status === 'uploading' || file.status === 'queued');
    },

    aggregateProgress() {
      const active = this.activeFiles()

      if (!active.length) return 100

      return Math.round(active.reduce((sum, file) => sum + file.progress, 0) / active.length);
    },

    isUploading() {
      return this.activeFiles().length > 0
    },

    hasError() {
      return this.files.some((file) => file.status === 'error');
    },

    isInvalid() {
      return invalid || this.hasError()
    },

    previewFile() {
      return this.find(this.previewId)
    },

    init() {
      bind(this.$refs.fileInput, {
        ['@change'](e) {
          const target = e.target
          this.addFiles(target.files)
          target.value = ''
        }
      })

      if (!droppable) return

      bind(this.$root.querySelector(dataKey('upload-dropzone')), {
        ['@dragover.prevent']() {
          this.dragOver = true
        },

        ['@dragleave.prevent'](e) {
          if (e.currentTarget && (e.currentTarget).contains(e.relatedTarget)) return

          this.dragOver = false
        },

        ['@drop.prevent'](e) {
          this.dragOver = false
          this.addFiles(e.dataTransfer?.files ?? null)
        }
      })

    },

    destroy() {
      this.files.forEach((file) => this.revoke(file))
    },

    selectFile() {
      (this.$refs.fileInput).click()
    },

    viewFile(id) {
      this.previewId = id

      if (! this.previewFile()) {
        this.previewId = null
        return
      }

      if (this.previewFile().previewLoaded) {
        this.$dispatch('modal-show', { name: previewName })
        return
      }

      this.openFile()
    },

    openFile() {
      const url = this.previewFile()?.url

      if (!url) {
        return
      }

      window.open(url, '_blank', 'noopener')
    },

    addFiles(fileList) {
      if (!fileList?.length) return

      if (!this.multiple()) {
        if (this.activeId) {
          this.cancelUpload(this.activeId)
        }

        this.files.forEach((file) => this.revoke(file))
        this.files = []
        this.queue = []
      }

      const incoming = Array.from(fileList)

      const remaining = this.multiple()
        ? (maxFiles ? Math.max(maxFiles - this.files.length, 0) : Infinity)
        : 1

      const accepted = incoming.slice(0, remaining)
      const rejected = this.multiple() && maxFiles ? incoming.slice(remaining) : []

      accepted.forEach((raw) => {
        const entry = this.createFileEntry(raw)

        this.files.push(entry)

        if (!entry.error) {
          this.queue.push(entry.id)
        }
      })

      rejected.forEach((raw) => {
        const entry = {
          id: generateId('upload-file'),
          raw,
          name: raw.name,
          size: raw.size,
          url: null,
          value: null,
          type: detectFileType(raw.type, raw.name),
          status: 'error',
          progress: 0,
          error: tooManyFilesMessage,
          tmpFilename: null,
        }

        this.files.push(entry)
      })

      this.processQueue()
      this.syncFieldError()
    },

    createFileEntry(raw) {
      const type = detectFileType(raw.type, raw.name)
      const previewable = PREVIEWABLE_TYPES.includes(type)
      const url = previewable ? URL.createObjectURL(raw) : null
      const error = this.validate(raw)

      return {
        id: generateId('upload-file'),
        raw,
        name: raw.name,
        size: raw.size,
        url,
        value: null,
        type,
        status: error ? 'error' : 'queued',
        progress: 0,
        error,
        tmpFilename: null,
        previewLoaded: previewable,
      }
    },

    validate(file) {
      if (maxSize && file.size > maxSize * 1024) {
        return tooLargeMessage
      }

      if (this.accept() && !this.matchesAccept(file, this.accept())) {
        return invalidTypeMessage
      }

      return null
    },

    matchesAccept(file, accept) {
      return accept.split(',').some((rule) => {
        rule = rule.trim()

        if (!rule) return false
        if (rule.startsWith('.')) return file.name.toLowerCase().endsWith(rule.toLowerCase())
        if (rule.endsWith('/*')) return file.type.startsWith(rule.slice(0, -1))

        return file.type === rule
      });
    },

    processQueue() {
      if (this.activeId || !this.queue.length) {
        return
      }

      const entry = this.find(this.queue.shift())

      if (!entry) {
        this.processQueue()
        return
      }

      this.activeId = entry.id
      entry.status = 'uploading'

      if (!this.$wire || !wireModel) {
        entry.status = 'done'
        entry.progress = 100
        this.activeId = null
        this.$nextTick(() => this.processQueue())
        return
      }

      this.$wire.upload(
        wireModel,
        entry.raw,
        (tmpFilename) => {
          entry.status = 'done'
          entry.progress = 100
          entry.tmpFilename = tmpFilename
          this.activeId = null
          this.processQueue()
          this.syncFieldError()
        },
        (message) => {
          entry.status = 'error'
          entry.error = message || 'Upload failed.'
          this.activeId = null
          this.processQueue()
          this.syncFieldError()
        },
        (e) => {
          entry.progress = e.detail.progress
        },
        () => {
          entry.status = 'cancelled'
          this.activeId = null
          this.processQueue()
        },
      )
    },

    retryUpload(id) {
      const entry = this.find(id)
      if (!entry?.raw) return

      entry.status = 'queued'
      entry.error = null
      entry.progress = 0
      this.queue.unshift(entry.id)
      this.processQueue()
    },

    cancelUpload(id) {
      if (id !== this.activeId || !this.$wire || !wireModel) return

      this.$wire.cancelUpload(wireModel)
    },

    removeFile(id) {
      const index = this.files.findIndex((file) => file.id === id)
      if (index === -1) return

      const entry = this.files[index]

      if (entry.id === this.activeId) {
        this.cancelUpload(id)
      } else {
        this.queue = this.queue.filter((queuedId) => queuedId !== id)
      }

      this.detachFromWire(entry, this.files.filter((file) => file.id !== id))

      this.revoke(entry)
      this.files.splice(index, 1)
      this.syncFieldError()
    },

    replaceFile(index, fileList) {
      const raw = fileList?.[0]
      const entry = this.files[index]
      if (!raw || !entry) return

      if (entry.id === this.activeId) {
        this.cancelUpload(entry.id)
      } else {
        this.queue = this.queue.filter((queuedId) => queuedId !== entry.id)
      }

      this.detachFromWire(entry, this.files.filter((file) => file.id !== entry.id))
      this.revoke(entry)

      const next = this.createFileEntry(raw)
      this.files.splice(index, 1, next)

      if (!next.error) {
        this.queue.push(next.id)
        this.processQueue()
      }

      this.syncFieldError()
    },

    detachFromWire(entry, remainingFiles) {
      if (!this.$wire || !wireModel) return

      if (entry.tmpFilename) {
        this.$wire.removeUpload(wireModel, entry.tmpFilename)
      } else if (entry.value !== null) {
        if (this.multiple()) {
          if (!this.hasPendingUploads()) {
            this.$wire.set(wireModel, remainingFiles
              .filter((file) => file.value !== null)
              .map((file) => file.value))
          }
        } else {
          this.$wire.set(wireModel, null)
        }
      }
    },

    syncFieldError() {
      if (this.isInvalid()) return

      findInField(this.$root, 'error')?.remove()
    },

    revoke(entry) {
      if (entry.raw && entry.url) {
        URL.revokeObjectURL(entry.url)
      }
    },

    find(id) {
      return this.files.find((file) => file.id === id) ?? null;
    },

    dragStart(index, e) {
      this.dragIndex = index
      e.dataTransfer?.setData('text/plain', String(index))
    },

    dragOverTile(index) {
      if (this.dragIndex === index) return

      this.dragOverIndex = index
    },

    dragLeaveTile(index, e) {
      if (this.dragOverIndex !== index) return
      if (e.currentTarget && (e.currentTarget).contains(e.relatedTarget)) return

      this.dragOverIndex = null
    },

    dropOnTile(index, e) {
      this.dragOverIndex = null
      this.dragOver = false

      const fileList = e.dataTransfer?.files

      if (fileList?.length) {
        this.replaceFile(index, fileList)
        return
      }

      this.drop(index)
    },

    drop(index) {
      if (this.dragIndex === null || this.dragIndex === index) return

      const [moved] = this.files.splice(this.dragIndex, 1)
      this.files.splice(index, 0, moved)
      this.dragIndex = null

      if (this.multiple() && this.$wire && wireModel && !this.hasPendingUploads()) {
        this.$wire.set(wireModel, this.files
          .filter((file) => file.value !== null)
          .map((file) => file.value))
      }
    },

    dragEnd() {
      this.dragIndex = null
      this.dragOverIndex = null
    },

    formatSize(bytes) {
      return formatBytes(bytes)
    },
  };
}

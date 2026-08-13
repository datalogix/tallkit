import { bind, formatBytes, generateId } from '../utils'

type FileStatus = 'queued' | 'uploading' | 'done' | 'error' | 'cancelled'

interface UploadFile {
  id: string
  raw: File | null
  name: string
  size: number
  url: string | null
  value: string | null
  type: string
  status: FileStatus
  progress: number
  error: string | null
  tmpFilename: string | null
}

function typeFromFile(file: File): string {
  const extension = file.name.split('.').pop()?.toLowerCase() ?? ''

  if (file.type.startsWith('image/')) return 'image'
  if (file.type.startsWith('video/')) return 'video'
  if (file.type.startsWith('audio/')) return 'audio'

  switch (extension) {
    case 'jpg': case 'jpeg': case 'png': case 'gif': return 'image'
    case 'mp4': return 'video'
    case 'mp3': return 'audio'
    case 'pdf': return 'pdf'
    case 'doc': case 'docx': return 'doc'
    case 'xls': case 'xlsx': return 'xls'
    case 'ppt': case 'pptx': return 'ppt'
    case 'rar': case 'zip': case '7z': return 'archive'
    case 'txt': case 'md': return 'text'
    case 'csv': return 'csv'
    case 'json': case 'js': case 'ts': case 'html': case 'css': return 'code'
    default: return 'unknown'
  }
}

const PREVIEWABLE_TYPES = ['image', 'video', 'audio', 'pdf']

export function upload({
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
}: {
  wireModel?: string | false
  multiple?: boolean
  droppable?: boolean
  maxSize?: number | null
  maxFiles?: number | null
  sortable?: boolean
  invalid?: boolean
  files?: Partial<UploadFile>[]
  tooLargeMessage?: string
  invalidTypeMessage?: string
  tooManyFilesMessage?: string
} = {}) {
  return {
    dragOver: false,
    dragIndex: null as number | null,
    sortable,

    files: files.map((file) => ({
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
    })) as UploadFile[],

    queue: [] as string[],
    activeId: null as string | null,

    get multiple() {
      return this.$refs.fileInput?.multiple ?? multiple
    },

    get accept() {
      return this.$refs.fileInput?.accept || null
    },

    init() {
      bind(this.$refs.fileInput, {
        ['@change'](event: Event) {
          const target = event.target as HTMLInputElement
          this.addFiles(target.files)
          target.value = ''
        }
      })

      if (!droppable) return

      bind(this.$root.querySelector('[data-tallkit-upload-dropzone]'), {
        ['@dragover.prevent']() {
          this.dragOver = true
        },

        ['@dragleave.prevent']() {
          this.dragOver = false
        },

        ['@drop.prevent'](event: DragEvent) {
          this.dragOver = false
          this.addFiles(event.dataTransfer?.files ?? null)
        }
      })

    },

    destroy() {
      this.files.forEach((file: UploadFile) => this.revoke(file))
    },

    selectFile() {
      this.$refs.fileInput.click()
    },

    addFiles(fileList: FileList | null) {
      if (!fileList?.length) return

      if (!this.multiple) {
        if (this.activeId) {
          this.cancelUpload(this.activeId)
        }

        this.files.forEach((file: UploadFile) => this.revoke(file))
        this.files = []
        this.queue = []
      }

      const incoming = Array.from(fileList)

      const remaining = this.multiple
        ? (maxFiles ? Math.max(maxFiles - this.files.length, 0) : Infinity)
        : 1

      const accepted = incoming.slice(0, remaining)
      const rejected = this.multiple && maxFiles ? incoming.slice(remaining) : []

      accepted.forEach((raw) => {
        const type = typeFromFile(raw)
        const url = PREVIEWABLE_TYPES.includes(type) ? URL.createObjectURL(raw) : null
        const error = this.validate(raw)

        const entry: UploadFile = {
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
        }

        this.files.push(entry)

        if (!error) {
          this.queue.push(entry.id)
        }
      })

      rejected.forEach((raw) => {
        const entry: UploadFile = {
          id: generateId('upload-file'),
          raw,
          name: raw.name,
          size: raw.size,
          url: null,
          value: null,
          type: typeFromFile(raw),
          status: 'error',
          progress: 0,
          error: tooManyFilesMessage,
          tmpFilename: null,
        }

        this.files.push(entry)
      })

      this.processQueue()
    },

    validate(file: File): string | null {
      if (maxSize && file.size > maxSize * 1024) {
        return tooLargeMessage
      }

      if (this.accept && !this.matchesAccept(file, this.accept)) {
        return invalidTypeMessage
      }

      return null
    },

    matchesAccept(file: File, accept: string): boolean {
      return accept.split(',').some((rule) => {
        rule = rule.trim()

        if (!rule) return false
        if (rule.startsWith('.')) return file.name.toLowerCase().endsWith(rule.toLowerCase())
        if (rule.endsWith('/*')) return file.type.startsWith(rule.slice(0, -1))

        return file.type === rule
      })
    },

    processQueue() {
      if (this.activeId || !this.queue.length) return

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
        (tmpFilename: string) => {
          entry.status = 'done'
          entry.progress = 100
          entry.tmpFilename = tmpFilename
          this.activeId = null
          this.processQueue()
        },
        (message: string) => {
          entry.status = 'error'
          entry.error = message || 'Upload failed.'
          this.activeId = null
          this.processQueue()
        },
        (event: { detail: { progress: number } }) => {
          entry.progress = event.detail.progress
        },
        () => {
          entry.status = 'cancelled'
          this.activeId = null
          this.processQueue()
        },
      )
    },

    retryUpload(id: string) {
      const entry = this.find(id)
      if (!entry?.raw) return

      entry.status = 'queued'
      entry.error = null
      entry.progress = 0
      this.queue.unshift(entry.id)
      this.processQueue()
    },

    cancelUpload(id: string) {
      if (id !== this.activeId || !this.$wire || !wireModel) return

      this.$wire.cancelUpload(wireModel)
    },

    removeFile(id: string) {
      const index = this.files.findIndex((file: UploadFile) => file.id === id)
      if (index === -1) return

      const entry = this.files[index]

      if (entry.id === this.activeId) {
        this.cancelUpload(id)
      } else {
        this.queue = this.queue.filter((queuedId: string) => queuedId !== id)
      }

      if (this.$wire && wireModel) {
        if (entry.tmpFilename) {
          this.$wire.removeUpload(wireModel, entry.tmpFilename)
        } else if (entry.value !== null) {
          if (this.multiple) {
            if (!this.hasPendingUploads) {
              this.$wire.set(wireModel, this.files
                .filter((file: UploadFile) => file.id !== id && file.value !== null)
                .map((file: UploadFile) => file.value))
            }
          } else {
            this.$wire.set(wireModel, null)
          }
        }
      }

      this.revoke(entry)
      this.files.splice(index, 1)
    },

    revoke(entry: UploadFile) {
      if (entry.raw && entry.url) {
        URL.revokeObjectURL(entry.url)
      }
    },

    find(id: string | undefined): UploadFile | null {
      return this.files.find((file: UploadFile) => file.id === id) ?? null
    },

    dragStart(index: number, event: DragEvent) {
      this.dragIndex = index
      event.dataTransfer?.setData('text/plain', String(index))
    },

    drop(index: number) {
      if (this.dragIndex === null || this.dragIndex === index) return

      const [moved] = this.files.splice(this.dragIndex, 1)
      this.files.splice(index, 0, moved)
      this.dragIndex = null

      if (this.multiple && this.$wire && wireModel && !this.hasPendingUploads) {
        this.$wire.set(wireModel, this.files
          .filter((file: UploadFile) => file.value !== null)
          .map((file: UploadFile) => file.value))
      }
    },

    dragEnd() {
      this.dragIndex = null
    },

    formatSize(bytes: number) {
      return formatBytes(bytes)
    },

    get activeFiles() {
      return this.files.filter((file: UploadFile) => file.status === 'uploading' || file.status === 'queued')
    },

    get hasPendingUploads() {
      return this.files.some((file: UploadFile) =>
        file.status === 'uploading' || file.status === 'queued')
    },

    get aggregateProgress() {
      const active = this.activeFiles

      if (!active.length) return 100

      return Math.round(active.reduce((sum: number, file: UploadFile) => sum + file.progress, 0) / active.length)
    },

    get isUploading() {
      return this.activeFiles.length > 0
    },

    get hasError() {
      return this.files.some((file: UploadFile) => file.status === 'error')
    },

    get isInvalid() {
      return invalid || this.hasError
    },
  }
}

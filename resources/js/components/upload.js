import { dataSelector, queryData, eventName, bind, formatBytes, detectFileType, generateId, findInField, onLivewireCommit, onFormReset, isRtl } from '../utils'

const PREVIEWABLE_TYPES = ['image', 'video', 'audio', 'pdf']

export function upload(
  {
    wireModel = false,
    multiple = false,
    droppable = true,
    maxSize = null,
    maxSizes = {},
    maxFiles = null,
    sortable = false,
    invalid = false,
    files = [],
    tooLargeMessage = 'The file may not be larger than :size.',
    invalidTypeMessage = 'This file type is not allowed.',
    tooManyFilesMessage = 'Too many files selected.',
    uploadFailedMessage = 'The file could not be uploaded.',
    movedMessage = 'Moved to position :position of :total.',
    sortHint = 'Drag, or press Alt and an arrow key, to move it.',
    sortHintId = null,
    previewName = null,
    fileTypes = {}
  } = {}
) {
  const fromServer = (file) => ({
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
    clientError: false,
    tmpFilename: file.tmpFilename ?? null,
    previewLoaded: false,
    previewFailed: false,
  })

  return {
    dragOver: false,
    dragIndex: null,
    dragOverIndex: null,
    sortable,
    previewId: null,
    announcement: '',
    sortHint,
    sortHintId,

    files: files.map(fromServer),

    queue: [],
    batchIds: [],
    activeId: null,

    needsValueSync: false,
    _stopCommits: null,

    wired() {
      return !!(this.$wire && wireModel)
    },

    multiple() {
      return (this.$refs.fileInput)?.multiple ?? multiple
    },

    accept() {
      return (this.$refs.fileInput)?.accept || null
    },

    activeFiles() {
      return this.files.filter((file) => file.status === 'uploading' || file.status === 'queued')
    },

    hasPendingUploads() {
      return this.files.some((file) =>
        file.status === 'uploading' || file.status === 'queued')
    },

    aggregateProgress() {
      if (!this.batchIds.length) return 100

      const total = this.batchIds.reduce((sum, id) => {
        const file = this.find(id)

        if (!file || file.status === 'done' || file.status === 'error' || file.status === 'cancelled') {
          return sum + 100
        }

        return sum + file.progress
      }, 0)

      return Math.round(total / this.batchIds.length)
    },

    isUploading() {
      return this.activeFiles().length > 0
    },

    hasError() {
      return this.files.some((file) => file.status === 'error')
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
          const picked = Array.from(target.files ?? [])

          if (this.wired()) target.value = ''

          this.addFiles(picked)
        }
      })

      if (this.wired()) {
        this._stopCommits = onLivewireCommit(({ component, succeed }) => {
          if (!component?.el?.contains(this.$root)) return

          succeed(() => this.$nextTick(() => this.syncFromServer()))
        })
      }

      if (!this.wired() && this.$refs.fileInput?.form) {
        const initial = this.files.map((file) => ({ ...file }))

        onFormReset(this.$root, this.$refs.fileInput.form, () => {
          this.files.forEach((file) => this.revoke(file))
          this.queue = []
          this.batchIds = []
          this.activeId = null
          this.previewId = null
          this.files = initial.map((file) => ({ ...file }))
          this.syncInput()
        })
      }

      if (!droppable) return

      bind(queryData(this.$root, 'upload-dropzone'), {
        ['@dragover.prevent']() {
          if (this.dragIndex !== null) return

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
      this._stopCommits?.()
    },

    syncInput() {
      if (this.wired() || !this.$refs.fileInput) return

      try {
        const transfer = new DataTransfer()

        this.files
          .filter((file) => file.raw && file.status !== 'error')
          .forEach((file) => transfer.items.add(file.raw))

        this.$refs.fileInput.files = transfer.files
      } catch {
      }
    },

    syncFromServer() {
      if (!this.wired() || this.activeId || this.hasPendingUploads()) return

      const server = [].concat(this.$wire.get(wireModel) ?? []).filter((value) => value !== null && value !== '')
      const shown = this.files.filter((file) => file.status === 'done')
      const holds = (file, value) => (file.value !== null && file.value === value)
        || (!!file.tmpFilename && typeof value === 'string' && value.endsWith(`:${file.tmpFilename}`))

      if (server.length === shown.length && server.every((value) => shown.some((file) => holds(file, value)))) return

      let state = this.$root.nextElementSibling

      while (state && !state.matches(dataSelector('upload-state'))) state = state.nextElementSibling

      if (!state) return

      try {
        const described = JSON.parse(state.textContent || '[]')

        const next = described.map((file) => {
          const entry = fromServer(file)
          const before = entry.tmpFilename && this.files.find((shown) => shown.tmpFilename === entry.tmpFilename)

          if (before && !entry.url && before.url) {
            entry.url = before.url
            entry.raw = before.raw
            before.url = null
          }

          return entry
        })

        this.files.forEach((file) => this.revoke(file))
        this.files = next
      } catch {
      }
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
        this.$dispatch(eventName('modal-show'), { name: previewName })
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

        this.files.forEach((file) => {
          this.revoke(file)
          this.detachFromWire(file)
        })
        this.files = []
        this.queue = []
      }

      const incoming = Array.from(fileList)

      const kept = this.files.filter((file) => file.status !== 'error' && file.status !== 'cancelled').length
      const remaining = this.multiple()
        ? (maxFiles ? Math.max(maxFiles - kept, 0) : Infinity)
        : 1

      const accepted = incoming.slice(0, remaining)
      const rejected = this.multiple() && maxFiles ? incoming.slice(remaining) : []

      if (!this.activeId && !this.queue.length) {
        this.batchIds = []
      }

      accepted.forEach((raw) => {
        const entry = this.createFileEntry(raw)

        this.files.push(entry)

        if (!entry.error) {
          this.queue.push(entry.id)
          this.batchIds.push(entry.id)
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
          type: detectFileType(raw.type, raw.name, fileTypes),
          status: 'error',
          progress: 0,
          error: tooManyFilesMessage,
          clientError: true,
          tmpFilename: null,
        }

        this.files.push(entry)
      })

      this.processQueue()
      this.syncInput()
      this.syncFieldError()
    },

    createFileEntry(raw) {
      const type = detectFileType(raw.type, raw.name, fileTypes)
      const previewable = PREVIEWABLE_TYPES.includes(type)
      const error = this.validate(raw)
      const url = previewable && !error ? URL.createObjectURL(raw) : null

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
        clientError: !!error,
        tmpFilename: null,
        previewLoaded: false,
        previewFailed: false,
      }
    },

    validate(file) {
      const kind = detectFileType(file.type, file.name, fileTypes)
      const limit = maxSizes[kind] ?? maxSizes.default ?? maxSize

      if (limit && file.size > limit * 1024) {
        return tooLargeMessage.replaceAll(':size', formatBytes(limit * 1024))
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
        if (rule === '*/*') return true
        if (rule.startsWith('.')) return file.name.toLowerCase().endsWith(rule.toLowerCase())
        if (rule.endsWith('/*')) return file.type.startsWith(rule.slice(0, -1))

        return file.type === rule
      })
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

      if (!this.wired()) {
        entry.status = 'done'
        entry.progress = 100
        this.activeId = null
        this.$nextTick(() => this.processQueue())
        return
      }

      // Livewire only adds an upload to a list: an empty one starts as [].
      if (this.multiple() && !Array.isArray(this.$wire.get(wireModel))) {
        this.$wire.set(wireModel, [], false)
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
          this.syncValues()
        },
        (message) => {
          entry.status = 'error'
          entry.error = message || uploadFailedMessage
          this.activeId = null
          this.processQueue()
          this.syncValues()

          this.$nextTick(() => {
            const rendered = findInField(this.$root, 'error')?.textContent?.trim()

            if (rendered) entry.error = rendered
          })
        },
        (e) => {
          entry.progress = e.detail.progress
        },
        () => {
          entry.status = 'cancelled'
          this.activeId = null
          this.processQueue()
          this.syncValues()
        },
      )
    },

    canRetry(file) {
      return !!file.raw && (file.status === 'cancelled' || (file.status === 'error' && !file.clientError))
    },

    retryUpload(id) {
      const entry = this.find(id)
      if (!entry || !this.canRetry(entry)) return

      const kept = this.files.filter((file) => file !== entry && file.status !== 'error' && file.status !== 'cancelled').length

      if (this.multiple() && maxFiles && kept >= maxFiles) {
        entry.status = 'error'
        entry.error = tooManyFilesMessage
        entry.clientError = true
        return
      }

      entry.status = 'queued'
      entry.error = null
      entry.progress = 0
      this.queue.unshift(entry.id)

      if (!this.batchIds.includes(entry.id)) this.batchIds.push(entry.id)

      this.processQueue()
      this.syncInput()
      this.syncFieldError()
    },

    cancelUpload(id) {
      if (id !== this.activeId || !this.$wire || !wireModel) return

      this.$wire.cancelUpload(wireModel)

      setTimeout(() => {
        if (this.activeId === id) {
          this.activeId = null
          this.processQueue()
        }
      }, 3000)
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

      this.revoke(entry)
      this.files.splice(index, 1)

      this.detachFromWire(entry)
      this.syncInput()
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

      this.revoke(entry)

      const next = this.createFileEntry(raw)
      this.files.splice(index, 1, next)

      this.detachFromWire(entry)

      if (!next.error) {
        this.queue.push(next.id)
        this.batchIds.push(next.id)
        this.processQueue()
      }

      this.syncInput()
      this.syncFieldError()
    },

    detachFromWire(entry) {
      if (!this.$wire || !wireModel) return

      if (entry.tmpFilename) {
        // Livewire only removes an upload from a list that starts with one: otherwise the list is written again.
        const first = [].concat(this.$wire.get(wireModel) ?? [])[0]

        if (!this.multiple() || (typeof first === 'string' && first.startsWith('livewire-file:'))) {
          this.$wire.removeUpload(wireModel, entry.tmpFilename)
          return
        }

        this.needsValueSync = true
        this.syncValues()
        return
      }

      if (entry.value === null) return

      if (!this.multiple()) {
        this.$wire.set(wireModel, null)
        return
      }

      this.needsValueSync = true
      this.syncValues()
    },

    syncValues() {
      if (!this.needsValueSync || !this.$wire || !wireModel || this.hasPendingUploads()) return

      this.needsValueSync = false

      // Taken from the property: Livewire only knows a new upload in its signed form.
      const held = [].concat(this.$wire.get(wireModel) ?? [])
      const signed = (tmpFilename) => held.find((value) => typeof value === 'string'
        && value.startsWith('livewire-file:') && value.endsWith(`:${tmpFilename}`)) ?? null

      this.$wire.set(wireModel, this.files
        .map((file) => file.value ?? (file.tmpFilename ? signed(file.tmpFilename) : null))
        .filter((value) => value !== null && value !== undefined))
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
      return this.files.find((file) => file.id === id) ?? null
    },

    dragStart(index, e) {
      this.dragIndex = index
      e.dataTransfer?.setData('text/plain', String(index))
    },

    dragOverTile(index) {
      if (this.dragIndex === index) return
      if (this.dragIndex === null && !droppable) return

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

      const fileList = Array.from(e.dataTransfer?.files ?? [])

      if (fileList.length) {
        if (!droppable) return

        this.replaceFile(index, fileList)

        if (this.multiple() && fileList.length > 1) this.addFiles(fileList.slice(1))

        return
      }

      this.drop(index)
    },

    drop(index) {
      if (this.dragIndex === null || this.dragIndex === index) return

      this.move(this.dragIndex, index)
      this.dragIndex = null
    },

    move(from, to) {
      const [moved] = this.files.splice(from, 1)
      this.files.splice(to, 0, moved)

      if (this.multiple() && this.wired()) {
        this.needsValueSync = true
        this.syncValues()
      }

      this.syncInput()
    },

    moveByKey(index, key) {
      if (!this.sortable) return

      const rtl = isRtl(this.$root)
      const back = key === 'up' || key === (rtl ? 'right' : 'left')
      const to = index + (back ? -1 : 1)

      if (to < 0 || to >= this.files.length) return

      const focused = document.activeElement

      this.move(index, to)

      this.$nextTick(() => {
        if (focused?.isConnected && document.activeElement !== focused) focused.focus()

        this.announcement = ''
        this.$nextTick(() => {
          this.announcement = movedMessage
            .replaceAll(':position', String(to + 1))
            .replaceAll(':total', String(this.files.length))
        })
      })
    },

    dragEnd() {
      this.dragIndex = null
      this.dragOverIndex = null
    },

    formatSize(bytes) {
      return formatBytes(bytes)
    },
  }
}

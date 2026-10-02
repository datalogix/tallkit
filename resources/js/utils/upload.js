import { formatBytes } from './file'
import { toast } from '../toast'
import { emit } from './event'

export function readAsDataURL(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = () => resolve(reader.result)
    reader.onerror = () => reject(reader.error)
    reader.readAsDataURL(file)
  })
}

export function getCsrfToken() {
  const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/)
  return match ? decodeURIComponent(match[1]) : null
}

function text(messages, key, replace = {}) {
  const fallback = {
    tooLarge: 'The file may not be larger than :size.',
    invalidType: 'This file type is not allowed.',
    failed: 'The file could not be uploaded.',
  }

  return Object.entries(replace).reduce(
    (result, [name, value]) => result.replaceAll(`:${name}`, value),
    messages?.[key] ?? fallback[key],
  )
}

export async function uploadEditorFile(file, type, upload, messages = {}) {
  if (!upload?.url) {
    return readAsDataURL(file)
  }

  if (upload.maxSize && !(type in upload.maxSize)) {
    throw new Error(text(messages, 'invalidType'))
  }

  const limit = upload.maxSize?.[type]

  if (limit && file.size > limit * 1024) {
    throw new Error(text(messages, 'tooLarge', { size: formatBytes(limit * 1024) }))
  }

  const body = new FormData()
  body.append('file', file, file.name)
  if (limit) body.append('max_size', String(limit))

  const response = await fetch(upload.url, {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      Accept: 'application/json',
      'X-XSRF-TOKEN': getCsrfToken() ?? '',
    },
    body,
  }).catch(() => {
    throw new Error(text(messages, 'failed'))
  })

  const result = await response.json().catch(() => ({}))

  if (!response.ok || !result.url) {
    throw new Error(result.message || text(messages, 'failed'))
  }

  return result.url
}

export function reportUploadFailed(el, error, file, type, messages = {}, notify = true) {
  console.error('[tallkit] An upload failed.', error)

  const message = error instanceof Error && error.message ? error.message : text(messages, 'failed')
  const event = emit(el, 'upload-failed', { error, file, type, message }, { bubbles: true, cancelable: true })

  if (event?.defaultPrevented || !notify) return message

  if (window.__tallkitToastContainer?.isConnected) {
    toast().error({ message })
  } else {
    window.alert(message)
  }

  return message
}

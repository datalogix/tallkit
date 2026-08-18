export function dataKey(name: string, value?: string) {
  return value
    ? `[data-tallkit-${name}="${value}"]`
    : `[data-tallkit-${name}]`
}

export function escapeHtml(str?: string | null) {
  if (str == null) return str

  return str.replace(/[&<>"']/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  })[char] as string)
}

export function generateId(
  prefix?: string,
  name?: string,
  suffix?: string,
) {
  return slug([
    'tallkit',
    prefix,
    name ?? Math.random().toString(36).slice(2, 9),
    suffix,
  ].filter(Boolean).join('-'))
}

export function slug(str?: string) {
  return normalize(str, {
    replaceAccents: true,
    removeSpaces: true,
    replaceSpaces: '-',
    lowercase: true,
    mode: 'alphanumeric',
  })
}

export function normalize(str?: string, options?: Partial<{
  mode: 'alpha' | 'numeric' | 'alphanumeric'
  lowercase: boolean,
  uppercase: boolean,
  replaceAccents: boolean,
  removeSpaces: boolean,
  replaceSpaces: string,
}>) {
  if (!options || !str) return str

  const opts = {
    replaceAccents: false,
    removeSpaces: false,
    lowercase: false,
    uppercase: false,
    mode: undefined as 'alpha' | 'numeric' | 'alphanumeric' | undefined,
    ...options,
  }

  if (opts?.replaceAccents) {
    str = str.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
  }

  switch (opts.mode) {
    case 'alpha':
      str = str.replace(/[^a-z\s-]/gi, '')
      break
    case 'alphanumeric':
      str = str.replace(/[^a-z0-9\s-]/gi, '')
      break
    case 'numeric':
      str = str.replace(/[^0-9\s-]/g, '')
      break
  }

  if (opts?.removeSpaces) {
    str = str.replace(/\s+/g, ' ').trim()
  }

  if (opts?.replaceSpaces) {
    str = str.replace(/\s+/g, opts.replaceSpaces).trim()
  }

  if (opts.uppercase && !opts.lowercase) {
    str = str.toUpperCase()
  } else if (opts.lowercase && !opts.uppercase) {
    str = str.toLowerCase()
  }

  return str
}

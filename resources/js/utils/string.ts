export function normalize(str?: string, options?: Partial<{
  mode: 'alpha' | 'numeric' | 'alphanumeric'
  lowercase: boolean,
  uppercase: boolean,
  replaceAccents: boolean,
  removeSpaces: boolean,
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

  if (opts?.removeSpaces) {
    str = str.replace(/\s+/g, ' ').trim()
  }

  switch (opts.mode) {
    case 'alpha':
      str = str.replace(/[^a-z]/gi, '')
      break
    case 'alphanumeric':
      str = str.replace(/[^a-z0-9]/gi, '')
      break
    case 'numeric':
      str = str.replace(/[^0-9]/g, '')
      break
  }

  if (opts.uppercase && !opts.lowercase) {
    str = str.toUpperCase()
  } else if (opts.lowercase && !opts.uppercase) {
    str = str.toLowerCase()
  }

  return str
}

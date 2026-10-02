const STORAGES = { local: 'localStorage', session: 'sessionStorage' }

// Where site data is blocked, reaching for it throws.
function getBrowserStorage(type = 'local') {
  try {
    return STORAGES[type] ? window[STORAGES[type]] : null
  } catch (e) {
    return null
  }
}

export function browserStorage(type = 'local') {
  const store = () => getBrowserStorage(type)

  const getText = (key, fallback = null) => {
    const s = store()

    if (!key || !s) return fallback

    try {
      return s.getItem(key) ?? fallback
    } catch (e) {
      return fallback
    }
  }

  const setText = (key, text) => {
    const s = store()

    if (!key || !s) return false

    try {
      s.setItem(key, String(text))

      return true
    } catch (e) {
      return false
    }
  }

  const remove = (key) => {
    const s = store()

    if (!key || !s) return

    try {
      s.removeItem(key)
    } catch (e) {}
  }

  const get = (key, fallback = null) => {
    const text = getText(key)

    if (text === null) return fallback

    try {
      return JSON.parse(text)
    } catch (e) {
      return fallback
    }
  }

  const set = (key, value) => {
    try {
      return setText(key, JSON.stringify(value))
    } catch (e) {
      return false
    }
  }

  const getPart = (key, part, fallback = null) => {
    const stored = get(key)

    return isPlainObject(stored) && part in stored ? stored[part] : fallback
  }

  const setPart = (key, part, value) => {
    const stored = get(key)

    return set(key, { ...(isPlainObject(stored) ? stored : {}), [part]: value })
  }

  const removePart = (key, part) => {
    const stored = get(key)

    if (!isPlainObject(stored) || !(part in stored)) return

    const { [part]: removed, ...rest } = stored

    if (Object.keys(rest).length) set(key, rest)
    else remove(key)
  }

  // All keys read first: removing while iterating skips some.
  const keys = (prefix = '') => {
    const s = store()

    if (!s) return []

    try {
      return Array.from({ length: s.length }, (_, index) => s.key(index)).filter((key) => key?.startsWith(prefix))
    } catch (e) {
      return []
    }
  }

  return { getText, setText, remove, get, set, getPart, setPart, removePart, keys }
}

function isPlainObject(value) {
  return value !== null && typeof value === 'object' && !Array.isArray(value)
}

const local = browserStorage('local')

export const getStoredText = local.getText
export const setStoredText = local.setText
export const removeStored = local.remove
export const getStoredPart = local.getPart
export const setStoredPart = local.setPart
export const removeStoredPart = local.removePart

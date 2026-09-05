export function cache(name, {
  ttl = 1000 * 60 * 60, // 1h
  persist = true
} = {}) {
  const memory = new Map()

  return {
    getStorageKey(key) {
      return ['tallkit', 'cache', name, key].filter(Boolean).join(':')
    },

    get(key) {
      const mem = memory.get(key)

      if (mem) {
        if (Date.now() < mem.exp) {
          return mem.data
        }

        memory.delete(key)
      }

      if (persist) {
        try {
          const raw = localStorage.getItem(this.getStorageKey(key))
          if (!raw) return null

          const parsed = JSON.parse(raw)

          if (Date.now() > parsed.exp) {
            localStorage.removeItem(this.getStorageKey(key))
            return null
          }

          memory.set(key, parsed)

          return parsed.data
        } catch (e) {
          return null
        }
      }

      return null
    },

    set(key, data) {
      const entry = {
        data,
        exp: Date.now() + ttl
      }

      memory.set(key, entry)

      if (persist) {
        try {
          localStorage.setItem(this.getStorageKey(key), JSON.stringify(entry))
        } catch (e) {
          //
        }
      }
    },
  };
}

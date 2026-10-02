import { storageKey } from './naming'
import { browserStorage } from './storage'

export function createCache(name, {
  ttl = 1000 * 60 * 60, // 1h
  persist = true,
  storage = 'local',
} = {}) {
  const memory = new Map()
  const store = persist ? browserStorage(storage) : null

  const isFresh = (entry) => entry !== null && typeof entry === 'object' && Date.now() <= entry.exp

  return {
    getStorageKey(key) {
      return storageKey('cache', name, key)
    },

    get(key) {
      const mem = memory.get(key)

      if (mem) {
        if (isFresh(mem)) {
          return mem.data
        }

        memory.delete(key)
      }

      if (!store) return null

      const stored = store.get(this.getStorageKey(key))

      if (stored === null) return null

      if (!isFresh(stored)) {
        store.remove(this.getStorageKey(key))
        return null
      }

      memory.set(key, stored)

      return stored.data
    },

    set(key, data) {
      const entry = {
        data,
        exp: Date.now() + ttl
      }

      memory.set(key, entry)

      if (store) {
        this.prune()
        store.set(this.getStorageKey(key), entry)
      }
    },

    prune() {
      if (!store) return

      for (const key of store.keys(`${storageKey('cache', name)}.`)) {
        if (!isFresh(store.get(key))) store.remove(key)
      }
    },
  }
}

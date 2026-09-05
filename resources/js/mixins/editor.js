import { dataKey, getWireModelInfo, setFieldValue } from '../utils'

export const EDITOR_GROUP_ORDER = [
  'text', 'heading', 'color', 'size', 'script', 'align', 'link', 'list', 'media', 'table', 'quote', 'code',
]

export function parseMode(mode, groupOrder) {
  const tokens = (mode ?? '').trim().split(/\s+/).filter(Boolean)

  if (!tokens.length) {
    return null
  }

  if (tokens.includes('none')) {
    return []
  }

  tokens
    .filter((token) => token !== 'full' && !groupOrder.includes(token))
    .forEach((token) => console.warn(`[tallkit] Unknown editor mode group "${token}"`))

  if (tokens.includes('full')) {
    return groupOrder
  }

  return groupOrder.filter((group) => tokens.includes(group))
}

export function editorField() {
  return {
    input: null,
    _lastSynced: null,

    initField() {
      this.input = this.$root.querySelector(dataKey('control'))

      if (this.$wire) {
        const prop = getWireModelInfo(this.input)

        if (prop) {
          this.$wire.$watch(prop.name, (value) => {
            if (value === this._lastSynced || !this.isCompleted()) return
            this.applyExternalValue(value)
          })
        }
      }
    },

    sync(value) {
      this._lastSynced = value
      setFieldValue(this.input, value)
    },
  };
}

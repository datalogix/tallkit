import { dataKey, queryData, getWireModelInfo, setFieldValue, hasBlurModel, blurOnFocusLeave, onFormReset } from '../utils'

export const EDITOR_GROUP_ORDER = [
  'text', 'heading', 'color', 'size', 'script', 'align', 'link', 'list', 'media', 'table', 'quote', 'code',
]

export function parseToolbar(toolbar, groupOrder) {
  const tokens = (toolbar ?? '').trim().split(/\s+/).filter(Boolean)

  if (!tokens.length) {
    return null
  }

  if (tokens.includes('none')) {
    return []
  }

  tokens
    .filter((token) => token !== 'full' && !groupOrder.includes(token))
    .forEach((token) => console.warn(`[tallkit] Unknown editor toolbar group "${token}"`))

  if (tokens.includes('full')) {
    return groupOrder
  }

  return groupOrder.filter((group) => tokens.includes(group))
}

export function editorField() {
  return {
    // _lastSynced: its own change coming back from Livewire isn't applied again (the cursor would jump).
    input: null,
    _lastSynced: null,

    initField() {
      this.input = queryData(this.$root, 'control')

      if (hasBlurModel(this.input)) blurOnFocusLeave(this.$root, this.input)

      // Not with Livewire: the reset doesn't change the property.
      if (!getWireModelInfo(this.input)) onFormReset(this.$root, this.input?.form, () => {
        if (this.isCompleted()) this.applyExternalValue(this.input.value)
      })

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

    lockState() {
      if (this.input?.disabled) return 'disabled'
      if (this.input?.readOnly) return 'readonly'

      return null
    },

    followLockState(apply, toolbar = () => null) {
      const update = () => {
        const state = this.lockState()

        apply(state !== null)
        toolbar()?.toggleAttribute('inert', state !== null)

        if (state) {
          this.$root.setAttribute(dataKey('editor-state'), state)
        } else {
          this.$root.removeAttribute(dataKey('editor-state'))
        }
      }

      update()

      this._lockObserver?.disconnect()
      this._lockObserver = new MutationObserver(update)
      this._lockObserver.observe(this.input, { attributes: true, attributeFilter: ['disabled', 'readonly'] })
    },

    stopFollowingLockState() {
      this._lockObserver?.disconnect()
      this._lockObserver = null
    },
  }
}

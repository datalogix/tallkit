import { queryData, getWireModelInfo, setFieldValue, hasBlurModel, blurOnFocusLeave, emit, onFormReset } from '../utils'

export function bindableField({
  key,
  property = 'value',
  serialize = function () { return this[property] ?? null },
  deserialize = function (raw) { return raw || null },
  toWire = null,
} = {}) {
  return {
    field: null,

    dispatchPicked(value) {
      emit(this.field ?? this.$root, 'picked', { value })
    },

    init() {
      this.field = queryData(this.$root, key)

      if (!this.field) {
        return
      }

      const prop = this.$wire ? getWireModelInfo(this.field) : null

      if (prop) {
        this[property] = deserialize.call(this, this.$wire.get(prop.name) ?? null)

        // The property's new value, not the field's: Livewire may not have updated it yet.
        this.$wire.$watch(prop.name, (value) => {
          this[property] = deserialize.call(this, value ?? null)
        })
      }

      if (!prop && [null, undefined, ''].includes(this[property]) && this.field.value !== '') {
        this[property] = deserialize.call(this, this.field.value)
      }

      // Next tick: its x-model directive is set up after the component.
      if (!prop) {
        this.$nextTick(() => {
          const model = this.field._x_model
          if (!model || !window.Alpine?.effect) return

          let last

          const effect = window.Alpine.effect(() => {
            const value = model.get()

            if (!this.$root.isConnected) return queueMicrotask(() => window.Alpine.release(effect))

            // Only when the variable changed: the effect also runs on the component's own changes, and would undo them.
            const key = JSON.stringify(value ?? null)
            if (key === last) return
            last = key

            const next = deserialize.call(this, value ?? null)

            if (JSON.stringify(next) !== JSON.stringify(this[property] ?? null)) this[property] = next
          })
        })
      }

      // A hidden input has no default to go back to on reset.
      if (!prop && this.field.form) {
        let initial = null

        this.$nextTick(() => { initial = JSON.stringify(this[property] ?? null) })

        onFormReset(this.$root, this.field.form, () => {
          if (initial !== null) this[property] = deserialize.call(this, JSON.parse(initial))
        })
      }

      if (hasBlurModel(this.field)) {
        blurOnFocusLeave(this.$root, this.field, prop && toWire
          ? () => { if (/\blive\b/.test(prop.modifier)) this.$wire.$commit() }
          : undefined)
      }

      this.$watch(property, () => {
        if (prop && toWire) {
          const next = toWire.call(this)

          if (JSON.stringify(next) === JSON.stringify(this.$wire.get(prop.name) ?? null)) return

          const live = /\b(live|change)\b/.test(prop.modifier) && !/\bblur\b/.test(prop.modifier)

          this.$wire.set(prop.name, next, live)

          return
        }

        setFieldValue(this.field, serialize.call(this))

        if (!prop && this.field._x_model) this.field._x_model.set(toWire ? toWire.call(this) : serialize.call(this))
      })
    },
  }
}

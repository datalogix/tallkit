import { dataKey, getWireModelInfo, setFieldValue } from '../utils'

export function bindableField({
  key,
  property = 'value',
  serialize = function () { return this[property] ?? null },
  deserialize = function (raw) { return raw || null },
} = {}) {
  return {
    field: null,

    init() {
      this.field = this.$root.querySelector(dataKey(key))

      if (!this.field) {
        return
      }

      if (this.$wire) {
        const prop = getWireModelInfo(this.field)

        if (prop) {
          this[property] = deserialize.call(this, this.$wire.get(prop.name) ?? null)

          this.$wire.$watch(prop.name, () => {
            this[property] = deserialize.call(this, this.field.value || null)
          })
        }
      }

      this.$watch(property, () => setFieldValue(this.field, serialize.call(this)))
    },
  }
}

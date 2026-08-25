import { dataKey, bind } from '../utils'

export function copy(
  targetId = null,
  content = null
) {
  return {
    copied: false,
    timeout: null,

    findTarget () {
      if (targetId) {
        const target = document.getElementById(targetId)

        if (target) {
          return target
        }
      }

      const controlKey = dataKey('control')

      return this.$el.closest(dataKey('field-control'))?.querySelector(controlKey)
        ?? this.$el.previousElementSibling?.querySelector(controlKey)
        ?? this.$el.parentElement?.previousElementSibling?.querySelector(controlKey)
        ?? null
    },

    init() {
      const target = this.findTarget()

      if (! target && ! content) {
        this.$el.remove()

        return
      }

      if (! navigator.clipboard) {
        this.$el.disabled = true

        return
      }

      bind(this.$el, {
        [':aria-pressed']() {
          return this.copied
        },

        async ['@click']() {
          clearTimeout(this.timeout)

          this.copied = true
          this.$el.dispatchEvent(new CustomEvent('open'))

          const currentTarget = content ? null : this.findTarget()
          const text = content ?? ('value' in currentTarget ? currentTarget.value : currentTarget.innerText)
          await navigator.clipboard.writeText(text)
          currentTarget?.dispatchEvent(new Event('copied', { bubbles: true }))

          this.timeout = setTimeout(() => {
            this.$el.dispatchEvent(new CustomEvent('close'))
            this.copied = false
            this.timeout = null
          }, 1000)
        }
      })
    },

    destroy() {
      clearTimeout(this.timeout)
    }
  }
}

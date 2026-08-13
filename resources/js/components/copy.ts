import { bind } from '../utils'

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

      return this.$el.closest('[data-tallkit-field-control]')?.querySelector('[data-tallkit-control]')
        ?? this.$el.previousElementSibling?.querySelector('[data-tallkit-control]')
        ?? this.$el.parentElement?.previousElementSibling?.querySelector('[data-tallkit-control]')
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

          const text = content ?? ('value' in target ? target.value : target.innerText)
          await navigator.clipboard.writeText(text)
          target?.dispatchEvent(new Event('copied', { bubbles: true }))

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

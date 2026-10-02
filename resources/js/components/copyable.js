import { dataSelector, queryData, announce, bind, emit } from '../utils'
import { tooltip } from '../tooltip'

export function copyable(targetId = null, content = null) {
  return {
    copied: false,
    timeout: null,

    findTarget() {
      if (targetId) {
        const target = document.getElementById(targetId)

        if (target) {
          return target
        }
      }

      return queryData(this.$el.closest(dataSelector('field-control')), 'control')
        ?? queryData(this.$el.previousElementSibling, 'control')
        ?? queryData(this.$el.parentElement?.previousElementSibling, 'control')
    },

    init() {
      const target = this.findTarget()

      if (! target && ! content) {
        this.$el.remove()

        return
      }

      bind(this.$el, {
        async ['@click']() {
          clearTimeout(this.timeout)

          const currentTarget = content ? null : this.findTarget()
          const text = content ?? (currentTarget ? ('value' in currentTarget ? currentTarget.value : currentTarget.innerText) : null)

          if (text === null || !(await copyText(text))) {
            this.copied = false
            emit(this.$root, 'failed')

            return
          }

          this.copied = true
          this.$nextTick(() => {
            tooltip.show(this.$el)
            announce(this.$el.getAttribute('aria-label'))
          })
          emit(currentTarget, 'copied', {}, { bubbles: true })

          this.timeout = setTimeout(() => {
            tooltip.hide()
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

async function copyText(text) {
  try {
    if (navigator.clipboard?.writeText) {
      await navigator.clipboard.writeText(text)

      return true
    }
  } catch {
  }

  const area = document.createElement('textarea')
  area.value = text
  area.setAttribute('readonly', '')
  area.style.position = 'fixed'
  area.style.opacity = '0'
  document.body.appendChild(area)
  area.select()

  try {
    return document.execCommand('copy')
  } catch {
    return false
  } finally {
    area.remove()
  }
}

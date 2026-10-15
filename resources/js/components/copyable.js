import { dataKey, dataSelector, queryData, announce, bind, emit } from '../utils'
import { tooltip } from '../tooltip'

export function copyable({ targetId = null, content = null, failedText = null } = {}) {
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
          tooltip.hide(this.$el)
          this.copied = false

          const currentTarget = content ? null : this.findTarget()
          const text = content ?? (currentTarget ? ('value' in currentTarget ? currentTarget.value : currentTarget.innerText) : null)
          const eventTarget = currentTarget ?? this.$root

          if (text === null || text === '' || !(await copyText(text, this.$el))) {
            emit(eventTarget, 'failed', {}, { bubbles: true })

            if (failedText) {
              tooltip.show(this.$el, failedText)
              announce(failedText)
              this.reset()
            }

            return
          }

          this.copied = true
          this.$nextTick(() => {
            tooltip.show(this.$el)
            announce(this.$el.getAttribute(dataKey('tooltip')))
          })

          emit(eventTarget, 'copied', {}, { bubbles: true })
          this.reset()
        }
      })
    },

    reset() {
      this.timeout = setTimeout(() => {
        tooltip.hide(this.$el)
        this.copied = false
        this.timeout = null
      }, 1500)
    },

    destroy() {
      clearTimeout(this.timeout)
      tooltip.hide(this.$el)
    }
  }
}

async function copyText(value, anchor) {
  const text = String(value)

  try {
    if (navigator.clipboard?.writeText) {
      await navigator.clipboard.writeText(text)

      return true
    }
  } catch {
  }

  const active = document.activeElement
  const area = document.createElement('textarea')
  area.value = text
  area.setAttribute('readonly', '')
  area.setAttribute('aria-hidden', 'true')
  area.tabIndex = -1
  Object.assign(area.style, { position: 'fixed', top: '0', left: '0', opacity: '0', pointerEvents: 'none' })
  ;(anchor?.parentElement ?? document.body).appendChild(area)
  area.focus({ preventScroll: true })
  area.select()
  area.setSelectionRange(0, text.length)

  try {
    return document.execCommand('copy')
  } catch {
    return false
  } finally {
    area.remove()
    active?.focus?.({ preventScroll: true })
  }
}

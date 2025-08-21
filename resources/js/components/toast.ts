import { bind } from '../utils'

export function toast() {
  return {
    toasts: [],
    positions: ['top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right'],

    init() {
      bind(this.$el, {
        ['@toast.document'](e) {
          this.addToast(e.detail)
        }
      })
    },

    addToast(props) {
      const toast = window.Alpine.reactive({
        id: Date.now() + Math.random(),
        ...props,
        duration: props.duration ?? 5000,
        position: props.position ?? 'bottom-right',
        visible: false,
      })

      if (toast.position === 'top') {
        toast.position = 'top-right'
      }

      if (toast.position === 'bottom') {
        toast.position = 'bottom-right'
      }

      this.toasts.push(toast)
      this.$nextTick(() => toast.visible = true)

      if (toast.duration) {
        setTimeout(
          () => this.removeToast(toast.id),
          toast.duration
        )
      }
    },

    removeToast(id) {
      const toast = this.toasts.find(t => t.id === id)
      if (!toast) return

      toast.visible = false

      setTimeout(() => {
        this.toasts = this.toasts.filter(t => t.id !== id)
      }, 300)
    },

    getToastsByPosition(position) {
      return this.toasts.filter(t => t.position === position)
    }
  }
}


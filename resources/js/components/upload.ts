import { bind } from "../utils"

export function upload({ droppable = false } = {}) {
  return {
    dragOver: false,

    get multiple () {
      return this.$refs.fileInput?.multiple ?? false
    },

    init() {
      if (!droppable) return

      bind(this.$root.querySelector('[data-tallkit-upload-button]'), {
        ['@dragover.prevent']() {
          this.dragOver = true
        },

        ['@dragleave.prevent']() {
          this.dragOver = false
        },

        ['@drop.prevent'](event) {
          this.$refs.fileInput.files = event.dataTransfer.files
          this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }))
        }
      })
    },

    selectFile() {
      this.$refs.fileInput.click()
    },

    removeFile(name) {
      if (!confirm('Are you sure you want to proceed?')) {
        return
      }

      if (this.$wire) {
        this.$wire.set(name, null)
      }
    }
  }
}

import { initAlpine, setupAlpine } from './alpine'
import { appearance } from './appearance'
import { toast } from './toast'
import { loadScript, loadStyle } from './utils'

const tallkit = {
  appearance,
  toast,
  loadScript,
  loadStyle,
  modal: (name) => {
    const dialog = document.querySelector(`dialog[data-modal="${name}"]`)

    return {
      show: () => {
        dialog?.showModal()
      },

      close: () => {
        dialog?.close()
      }
    }
  },

  modals: () => {
    const dialogs = document.querySelectorAll(`dialog[data-tallkit-modal]`)

    return {
      close: () => {
        dialogs.forEach(modal => modal.close())
      }
    }
  }
}

window.TALLKit = window.TK = window.tk = window.tallkit = tallkit
document.dispatchEvent(new CustomEvent('tallkit:init'))

initAlpine()
document.addEventListener('alpine:init', setupAlpine)

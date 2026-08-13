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
    return {
      show: () => {
        document.dispatchEvent(new CustomEvent('modal-show', { detail: { name } }))
      },

      close: () => {
        document.dispatchEvent(new CustomEvent('modal-close', { detail: { name } }))
      }
    }
  },

  modals: () => {
    return {
      close: () => {
        document.dispatchEvent(new CustomEvent('modal-close', { detail: {} }))
      }
    }
  }
}

window.TALLKit = window.TK = window.tk = window.tallkit = tallkit
document.dispatchEvent(new CustomEvent('tallkit:init'))

initAlpine()
document.addEventListener('alpine:init', setupAlpine)

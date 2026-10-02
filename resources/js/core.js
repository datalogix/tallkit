import { install } from './alpine'
import { appearance } from './appearance'
import { consent } from './consent'
import { toast } from './toast'
import { tooltip } from './tooltip'
import { emit, eventName, loadScript, loadStyle } from './utils'

export const tallkit = {
  appearance,
  consent,
  toast,
  tooltip,
  loadScript,
  loadStyle,
  modal: (name) => {
    return {
      show: () => {
        emit(document, eventName('modal-show'), { name })
      },

      close: () => {
        emit(document, eventName('modal-close'), { name })
      }
    }
  },

  modals: () => {
    return {
      close: () => {
        emit(document, eventName('modal-close'))
      }
    }
  }
}

export function exposeGlobals() {
  if (window.tallkit) {
    return
  }

  window.TALLKit = window.TK = window.tk = window.tallkit = tallkit
  emit(document, eventName('init'))
}

export function plugin(Alpine) {
  exposeGlobals()
  install(Alpine, tallkit)
}

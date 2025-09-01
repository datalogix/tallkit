import { initAlpine, setupAlpine } from './alpine'
import { appearance } from './appearance'
import { toast } from './toast'
import { loadScript, loadStyle } from './utils'

const tallkit = {
  appearance,
  toast,
  loadScript,
  loadStyle,
}

window.TALLKit = window.TK = window.tk = window.tallkit = tallkit
document.dispatchEvent(new CustomEvent('tallkit:init'))

initAlpine()
document.addEventListener('alpine:init', setupAlpine)

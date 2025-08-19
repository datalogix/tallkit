import { initAlpine, setupAlpine } from './alpine'
import { appearance } from './appearance'
import { toast } from './toast'

const tallkit = {
  appearance,
  toast,
}

window.TALLKit = window.TK = window.tk = window.tallkit = tallkit
document.dispatchEvent(new CustomEvent('tallkit:init'))

initAlpine()
document.addEventListener('alpine:init', setupAlpine)

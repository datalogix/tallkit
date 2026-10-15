import { bootAlpine } from './alpine'
import { tallkit, exposeGlobals } from './core'

const script = document.currentScript
const load = script?.dataset.loadAlpine !== 'false'

exposeGlobals()

try {
  if (script?.dataset.tooltip) tallkit.tooltip.configure(JSON.parse(script.dataset.tooltip))
} catch {
  console.warn('[tallkit] The tooltip defaults on the script tag are not valid JSON.')
}

bootAlpine(tallkit, { load })

export { tallkit }

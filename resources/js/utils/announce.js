import { dataKey } from './naming'

let region = null

export function announce(text) {
  if (!text || typeof document === 'undefined') return

  if (!region || !region.isConnected) {
    region = document.createElement('div')
    region.setAttribute('aria-live', 'polite')
    region.setAttribute('aria-atomic', 'true')
    region.setAttribute(dataKey('announcer'), '')
    region.style.cssText = 'position:absolute;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0'
    document.body.appendChild(region)
  }

  region.textContent = ''
  setTimeout(() => { region.textContent = String(text) }, 50)
}

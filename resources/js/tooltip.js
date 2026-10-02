import {
  dataKey,
  dataSelector,
  generateId,
  isRtl,
  onLivewireCommit,
  placeNextTo,
  pushEscapeLayer,
  toMilliseconds,
  queryAllData,
  queryData,
  removeEscapeLayer,
} from './utils'

const DEFAULTS = { delay: 200, position: 'top', align: 'center', arrow: true, variant: null, size: null }
const WARM_MS = 300
const LEAVE_MS = 100
const MARGIN = 4
const ARROW_MARGIN = 10
const INTERACTIVE = 'a[href], button, input, select, textarea, summary, [role=button], [role=link]'
const FOCUSABLE = 'a[href], button, input:not([type=hidden]), select, textarea, summary, [tabindex]:not([tabindex="-1"])'
const HOLD = { hover: 0, touch: 1, focus: 2, manual: 3 }
const TRIGGER = dataSelector('tooltip')
const CSS_VARIABLES = ['--tk-tooltip-max-width', '--tk-tooltip-duration']
const SCRIPT_TIP_PREFIX = `${generateId('tip-js', '')}-`

const config = { ...DEFAULTS }

let installed = false
let panel = null
let descriptions = null
let current = null
let pending = null
let openTimer = null
let closeTimer = null
let warmUntil = 0
let suppressed = null
let lastPointerType = 'mouse'
let resizeObserver = null
let frame = null
let pruneScheduled = false

const layer = { close: () => hide(), popoverElement: null, ariaTrigger: null }

export function installTooltips() {
  if (installed) return

  installed = true

  const on = (type, handler) => document.addEventListener(type, handler, { capture: true, passive: true })

  on('pointerover', onPointerOver)
  on('pointerout', onPointerOut)
  on('pointerdown', onPointerDown)
  on('click', onClick)
  on('focusin', onFocusIn)
  on('focusout', onFocusOut)

  onLivewireCommit(({ succeed }) => succeed(() => {
    describeAll()
    refresh()
  }))

  document.addEventListener('livewire:navigating', () => hide())
  document.addEventListener('livewire:navigated', () => describeAll())

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => describeAll(), { once: true })
  } else {
    describeAll()
  }
}

export const tooltip = {
  show(el, text = null) {
    if (!el) return

    const original = textOf(el)

    if (text !== null) el.setAttribute(dataKey('tooltip'), text)
    if (!el.hasAttribute(dataKey('tooltip'))) return

    show(el, 'manual')

    if (text === null || current?.trigger !== el) return

    fill(el)

    if (!('restore' in current)) current.restore = original
  },

  hide() {
    hide()
  },

  configure(options = {}) {
    Object.assign(config, options)

    config.arrow = config.arrow !== false && config.arrow !== 'false'
  },

  refresh() {
    describeAll()
  },
}

export function tooltipDirective(Alpine) {
  Alpine.directive('tooltip', (el, { expression, modifiers }, { effect, evaluateLater, cleanup }) => {
    const position = modifiers.find((m) => ['top', 'bottom', 'left', 'right'].includes(m))
    const align = modifiers.find((m) => ['start', 'end', 'center'].includes(m))

    if (position) el.setAttribute(dataKey('tooltip-position'), position)
    if (align) el.setAttribute(dataKey('tooltip-align'), align)
    if (modifiers.includes('arrow')) el.setAttribute(dataKey('tooltip-arrow'), 'true')
    if (modifiers.includes('no-arrow')) el.setAttribute(dataKey('tooltip-arrow'), 'false')
    if (modifiers.includes('manual')) el.setAttribute(dataKey('tooltip-open'), 'manual')

    const evaluate = evaluateLater(expression)

    effect(() => evaluate((value) => {
      if (value === null || value === undefined || value === false || value === '') {
        el.removeAttribute(dataKey('tooltip'))
        if (current?.trigger === el) hide()
      } else {
        el.setAttribute(dataKey('tooltip'), String(value))
        el.removeAttribute(dataKey('tooltip-id'))
        describe(el)
        schedulePrune()
        if (current?.trigger === el) fill(el)
      }
    }))

    cleanup(() => {
      if (current?.trigger === el) hide()
    })
  })
}

const textOf = (trigger) => trigger.getAttribute(dataKey('tooltip'))

const option = (trigger, name) => trigger.getAttribute(dataKey(`tooltip-${name}`))

const triggerOf = (el) => el?.closest?.(TRIGGER) ?? null
const isManual = (trigger) => option(trigger, 'open') === 'manual'
const holds = (el) => !!el && !!current && (current.trigger.contains(el) || panel?.contains(el))

function hasArrow(trigger) {
  const arrow = option(trigger, 'arrow')

  return arrow === null ? config.arrow : arrow !== 'false'
}

function controlOf(trigger) {
  const control = trigger.matches(dataSelector('control')) ? trigger : (queryData(trigger, 'control') ?? trigger)

  return control.matches(FOCUSABLE) ? control : (control.querySelector(FOCUSABLE) ?? control)
}

function onPointerOver(event) {
  if (event.pointerType === 'touch') return

  const target = event.target

  if (panel?.contains(target)) {
    cancelClose()
    return
  }

  if (suppressed && !suppressed.contains(target)) suppressed = null

  const trigger = triggerOf(target)

  if (!trigger || isManual(trigger) || trigger === suppressed) {
    scheduleClose()
    return
  }

  cancelClose()

  if (current?.trigger !== trigger) scheduleOpen(trigger)
}

function onPointerOut(event) {
  if (event.relatedTarget) return

  suppressed = null
  scheduleClose()
}

function onPointerDown(event) {
  lastPointerType = event.pointerType

  if (event.pointerType === 'touch') {
    if (current && !current.trigger.contains(event.target)) hide()
    return
  }

  const trigger = triggerOf(event.target)

  if (!trigger || isManual(trigger)) return

  suppressed = trigger
  cancelOpen()

  if (current?.trigger === trigger) hide()
}

function onClick(event) {
  if (lastPointerType !== 'touch') return

  const trigger = triggerOf(event.target)

  if (!trigger || isManual(trigger)) return
  if (trigger.matches(INTERACTIVE) || trigger.querySelector(INTERACTIVE)) return

  if (current?.trigger === trigger) {
    hide()
  } else {
    show(trigger, 'touch')
  }
}

function onFocusIn(event) {
  const trigger = triggerOf(event.target)

  if (trigger && !isManual(trigger) && event.target.matches?.(':focus-visible')) show(trigger, 'focus')
}

function onFocusOut(event) {
  if (current?.via === 'focus' && !holds(event.relatedTarget)) hide()
}

function scheduleOpen(trigger) {
  if (pending === trigger) return

  cancelOpen()

  const delay = toMilliseconds(option(trigger, 'delay') ?? config.delay)

  if (!(delay > 0) || current || Date.now() < warmUntil) {
    show(trigger, 'hover')
    return
  }

  pending = trigger
  openTimer = setTimeout(() => {
    const next = pending

    pending = null
    openTimer = null

    if (next?.isConnected) show(next, 'hover')
  }, delay)
}

function cancelOpen() {
  clearTimeout(openTimer)
  openTimer = null
  pending = null
}

function scheduleClose() {
  cancelOpen()

  if (!current || current.via !== 'hover' || closeTimer) return

  closeTimer = setTimeout(hide, LEAVE_MS)
}

function cancelClose() {
  clearTimeout(closeTimer)
  closeTimer = null
}

function panelFor(trigger) {
  if (!panel?.isConnected) {
    panel = document.createElement('div')
    panel.setAttribute('popover', 'manual')
    panel.setAttribute('role', 'tooltip')
    panel.id = generateId('tooltip', 'panel')
    panel.setAttribute(dataKey('tooltip-panel'), '')
    panel.innerHTML = `<span ${dataKey('tooltip-panel-arrow')} aria-hidden="true"></span>`
      + `<span ${dataKey('tooltip-panel-body')}>`
      + `<span ${dataKey('tooltip-panel-text')}></span><kbd ${dataKey('tooltip-panel-kbd')}></kbd>`
      + '</span>'
    document.body.appendChild(panel)
  }

  const host = trigger.closest('dialog[open]') ?? document.body

  if (panel.parentElement !== host) {
    if (panel.matches(':popover-open')) panel.hidePopover()
    host.appendChild(panel)
  }

  return panel
}

function fill(trigger) {
  const text = queryData(panel, 'tooltip-panel-text')
  const kbd = queryData(panel, 'tooltip-panel-kbd')
  const template = trigger.querySelector(`:scope > template${dataSelector('tooltip-content')}`)
  const shortcut = option(trigger, 'kbd')
  const color = option(trigger, 'color')

  if (template) {
    text.replaceChildren(template.content.cloneNode(true))
  } else {
    // Text only: a name from data never turns into markup.
    text.textContent = textOf(trigger) ?? ''
  }

  kbd.textContent = shortcut ?? ''
  kbd.hidden = !shortcut

  panel.className = option(trigger, 'class') ?? ''
  panel.dataset.variant = option(trigger, 'variant') ?? config.variant ?? ''
  panel.dataset.color = color ?? ''
  panel.dataset.size = option(trigger, 'size') ?? config.size ?? ''
  panel.toggleAttribute('data-arrow', hasArrow(trigger))

  if (color) panel.classList.add(`tk-color-${color}`)

  const style = getComputedStyle(trigger)

  for (const name of CSS_VARIABLES) {
    const value = style.getPropertyValue(name).trim()

    if (value) panel.style.setProperty(name, value)
    else panel.style.removeProperty(name)
  }
}

function show(trigger, via) {
  cancelOpen()
  cancelClose()

  if (current?.trigger === trigger) {
    if (HOLD[via] > HOLD[current.via]) current.via = via
    return
  }

  hide()

  if (!trigger.isConnected || !trigger.hasAttribute(dataKey('tooltip'))) return

  panelFor(trigger)
  fill(trigger)

  current = { trigger, via }

  if (!panel.matches(':popover-open')) panel.showPopover()

  layer.popoverElement = panel
  layer.ariaTrigger = controlOf(trigger)
  pushEscapeLayer(layer)

  window.addEventListener('scroll', reposition, true)
  window.addEventListener('resize', reposition, true)

  resizeObserver = new ResizeObserver(() => reposition())
  resizeObserver.observe(trigger)
  resizeObserver.observe(panel)

  place()
}

function hide() {
  cancelClose()

  if (!current) return

  const { trigger, restore } = current

  current = null
  warmUntil = Date.now() + WARM_MS

  if (restore !== undefined) {
    if (restore === null) trigger.removeAttribute(dataKey('tooltip'))
    else trigger.setAttribute(dataKey('tooltip'), restore)
  }

  removeEscapeLayer(layer)
  layer.popoverElement = layer.ariaTrigger = null

  window.removeEventListener('scroll', reposition, true)
  window.removeEventListener('resize', reposition, true)

  resizeObserver?.disconnect()
  resizeObserver = null

  cancelAnimationFrame(frame)
  frame = null

  if (panel?.isConnected && panel.matches(':popover-open')) panel.hidePopover()
}

function refresh() {
  if (!current) return

  if (!current.trigger.isConnected || !current.trigger.hasAttribute(dataKey('tooltip'))) {
    hide()
    return
  }

  fill(current.trigger)
  reposition()
}

function reposition() {
  if (!current || frame) return

  frame = requestAnimationFrame(() => {
    frame = null
    place()
  })
}

function place() {
  if (!current) return

  const { trigger } = current

  if (!trigger.isConnected || !panel?.isConnected) {
    hide()
    return
  }

  placeNextTo(panel, trigger.getBoundingClientRect(), {
    position: option(trigger, 'position') || config.position,
    align: option(trigger, 'align') || config.align,
    margin: hasArrow(trigger) ? ARROW_MARGIN : MARGIN,
    rtl: isRtl(trigger),
  })
}

function describeAll() {
  document.querySelectorAll(TRIGGER).forEach(describe)
  prune()
}

function describe(trigger) {
  if (isManual(trigger)) return

  const name = textOf(trigger)
  const text = [name, option(trigger, 'kbd')].filter(Boolean).join(' ')

  if (!text) return

  let id = option(trigger, 'id')

  if (!id) {
    const control = controlOf(trigger)

    if (normalizeName(nameOf(control)) === normalizeName(name)) return

    id = hashId(text)
    trigger.setAttribute(dataKey('tooltip-id'), id)

    const ids = (control.getAttribute('aria-describedby') ?? '')
      .split(' ')
      .filter((other) => other && !other.startsWith(SCRIPT_TIP_PREFIX))

    control.setAttribute('aria-describedby', [...ids, id].join(' '))
  }

  if (!document.getElementById(id)) {
    const span = document.createElement('span')

    span.id = id
    span.textContent = text
    descriptionsHost().appendChild(span)
  }
}

function descriptionsHost() {
  if (!descriptions?.isConnected) {
    descriptions = document.createElement('div')
    descriptions.hidden = true
    descriptions.setAttribute(dataKey('tooltip-descriptions'), '')
    document.body.appendChild(descriptions)
  }

  return descriptions
}

// prune() reads the whole page: once per pass, not once per x-tooltip.
function schedulePrune() {
  if (pruneScheduled) return

  pruneScheduled = true
  queueMicrotask(prune)
}

function prune() {
  pruneScheduled = false

  if (!descriptions?.isConnected) return

  const used = new Set(queryAllData(document, 'tooltip-id').map((el) => el.getAttribute(dataKey('tooltip-id'))))

  Array.from(descriptions.children).forEach((span) => {
    if (!used.has(span.id)) span.remove()
  })
}

function nameOf(control) {
  const labelledBy = control.getAttribute('aria-labelledby')

  if (control.getAttribute('aria-label')) return control.getAttribute('aria-label')
  if (labelledBy) return labelledBy.split(/\s+/).map((id) => document.getElementById(id)?.textContent ?? '').join(' ')
  if (control.labels?.length) return Array.from(control.labels).map((label) => label.textContent).join(' ')

  return control.textContent
}

const normalizeName = (text) => (text ?? '').replace(/\s+/g, ' ').trim().toLowerCase()

function hashId(text) {
  let hash = 0x811c9dc5

  for (let i = 0; i < text.length; i++) {
    hash ^= text.charCodeAt(i)
    hash = Math.imul(hash, 0x01000193)
  }

  return generateId('tip-js', (hash >>> 0).toString(36))
}

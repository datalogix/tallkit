import { clamp } from './number'

export function placeNextTo(panel, rect, { position = 'bottom', align = 'end', margin = 4, rtl = false } = {}) {
  if (!panel.offsetWidth && !panel.offsetHeight) return false

  // Measured from the origin: shrink-to-fit width depends on the room left of where it is.
  panel.style.position = 'absolute'
  panel.style.inset = 'auto'
  panel.style.top = '0px'
  panel.style.left = '0px'

  const scrollTop = window.scrollY
  const scrollLeft = window.scrollX
  // Without the scrollbars: innerWidth includes them.
  const viewportWidth = document.documentElement.clientWidth || window.innerWidth
  const viewportHeight = document.documentElement.clientHeight || window.innerHeight

  const panelHeight = panel.offsetHeight
  const panelWidth = panel.offsetWidth

  const resolveAlign = (align) => {
    if (align === 'start') return rtl ? 'right' : 'left'
    if (align === 'end') return rtl ? 'left' : 'right'
    return align
  }

  const getCenterOffset = (pos, align) => {
    align = resolveAlign(align)

    if (align === 'left') return 0
    if (align === 'right') {
      return pos === 'left' || pos === 'right'
        ? rect.height - panelHeight
        : rect.width - panelWidth
    }

    return pos === 'left' || pos === 'right'
      ? (rect.height - panelHeight) / 2
      : (rect.width - panelWidth) / 2
  }

  const getCoords = (pos, align) => {
    const center = getCenterOffset(pos, align)
    let top = 0, left = 0

    switch (pos) {
      case 'right':
        left = rect.right + margin + scrollLeft
        top = rect.top + center + scrollTop
        break
      case 'left':
        left = rect.left - panelWidth - margin + scrollLeft
        top = rect.top + center + scrollTop
        break
      case 'bottom':
        top = rect.bottom + margin + scrollTop
        left = rect.left + center + scrollLeft
        break
      case 'top':
        top = rect.top - panelHeight - margin + scrollTop
        left = rect.left + center + scrollLeft
        break
    }

    return { top, left }
  }

  const isVisible = ({ top, left }) => (
    top >= scrollTop &&
    left >= scrollLeft &&
    top + panelHeight <= scrollTop + viewportHeight &&
    left + panelWidth <= scrollLeft + viewportWidth
  )

  const opposites = { top: 'bottom', bottom: 'top', left: 'right', right: 'left' }
  const aligns = ['start', 'left', 'end', 'right', 'center']
  const logical = { start: rtl ? 'right' : 'left', end: rtl ? 'left' : 'right' }
  let computedPosition = logical[position] ?? (position || 'bottom')
  let computedAlign = align || 'end'
  let coords = getCoords(computedPosition, computedAlign)

  let found = false

  if (!isVisible(coords)) {
    const fallbacks = [
      opposites[computedPosition],
      ...['top', 'bottom', 'left', 'right'].filter(p => p !== computedPosition && p !== opposites[computedPosition]),
    ]

    for (const pos of [computedPosition, ...fallbacks]) {
      for (const al of [computedAlign, ...aligns.filter(a => a !== computedAlign)]) {
        const testCoords = getCoords(pos, al)

        if (isVisible(testCoords)) {
          computedPosition = pos
          computedAlign = al
          coords = testCoords
          found = true
          break
        }
      }

      if (found) break
    }
  }

  if (!found && !isVisible(coords)) {
    const gap = 8
    const fit = (value, size, start, room) => size + gap * 2 > room
      ? start + gap
      : clamp(value, start + gap, start + room - size - gap)

    coords = {
      top: fit(coords.top, panelHeight, scrollTop, viewportHeight),
      left: fit(coords.left, panelWidth, scrollLeft, viewportWidth),
    }
  }

  const vertical = computedPosition === 'left' || computedPosition === 'right'
  const size = vertical ? panelHeight : panelWidth
  const middle = vertical
    ? rect.top + rect.height / 2 + scrollTop - coords.top
    : rect.left + rect.width / 2 + scrollLeft - coords.left
  const corner = 12
  const arrowOffset = size < corner * 2 ? size / 2 : clamp(middle, corner, size - corner)

  panel.style.top = `${coords.top}px`
  panel.style.left = `${coords.left}px`
  panel.style.setProperty('--tk-arrow-offset', `${arrowOffset}px`)
  panel.dataset.position = computedPosition
  panel.dataset.align = computedAlign === 'center' ? 'center' : resolveAlign(computedAlign)

  return true
}

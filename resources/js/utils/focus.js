export const FOCUSABLE = [
  'a[href]',
  'area[href]',
  'button:not([disabled])',
  'input:not([disabled]):not([type="hidden"])',
  'select:not([disabled])',
  'textarea:not([disabled])',
  'summary',
  '[tabindex]:not([tabindex="-1"])',
  '[contenteditable]:not([contenteditable="false"])',
].join(',')

// Hidden by display:none or an ancestor's: it has no boxes.
export function isRendered(el) {
  return el.getClientRects().length > 0
}

export function focusTargetOutside(el) {
  const candidates = Array.from(document.querySelectorAll(FOCUSABLE))
    .filter((node) => !el.contains(node) && !node.closest('[hidden], [inert]') && isRendered(node))

  return candidates.find((node) => el.compareDocumentPosition(node) & Node.DOCUMENT_POSITION_FOLLOWING)
    ?? candidates.reverse().find((node) => el.compareDocumentPosition(node) & Node.DOCUMENT_POSITION_PRECEDING)
    ?? null
}

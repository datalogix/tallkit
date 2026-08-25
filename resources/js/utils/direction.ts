export function isRtl(el = document.documentElement) {
  return el.dir === 'rtl' || getComputedStyle(el).direction === 'rtl'
}

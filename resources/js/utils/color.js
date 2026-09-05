const HEX_RE = /^#([0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i
const RGB_RE = /^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*([\d.]+%?)\s*)?\)$/i
const HSL_RE = /^hsla?\(\s*([\d.]+)\s*,\s*([\d.]+)%\s*,\s*([\d.]+)%\s*(?:,\s*([\d.]+%?)\s*)?\)$/i

function clamp255(value) {
  return Math.max(0, Math.min(255, Math.round(Number(value))))
}

function clampAlpha(value) {
  return Math.max(0, Math.min(1, Number.isFinite(value) ? value : 1))
}

function roundAlpha(value) {
  return parseFloat(clampAlpha(value).toFixed(2))
}

function parseAlpha(value) {
  if (value === undefined) return 1

  return clampAlpha(value.endsWith('%') ? parseFloat(value) / 100 : parseFloat(value))
}

function hexToRgba(hex) {
  const short = hex.length === 3 || hex.length === 4

  const r = short ? hex[0] + hex[0] : hex.slice(0, 2)
  const g = short ? hex[1] + hex[1] : hex.slice(2, 4)
  const b = short ? hex[2] + hex[2] : hex.slice(4, 6)
  const a = short ? (hex.length === 4 ? hex[3] + hex[3] : null) : (hex.length === 8 ? hex.slice(6, 8) : null)

  return {
    r: parseInt(r, 16),
    g: parseInt(g, 16),
    b: parseInt(b, 16),
    a: a === null ? 1 : parseInt(a, 16) / 255,
  }
}

function hslToRgb(h, s, l) {
  h = ((h % 360) + 360) % 360
  s = clampAlpha(s / 100)
  l = clampAlpha(l / 100)

  const c = (1 - Math.abs(2 * l - 1)) * s
  const x = c * (1 - Math.abs(((h / 60) % 2) - 1))
  const m = l - c / 2

  const [r, g, b] = (
    h < 60 ? [c, x, 0] :
    h < 120 ? [x, c, 0] :
    h < 180 ? [0, c, x] :
    h < 240 ? [0, x, c] :
    h < 300 ? [x, 0, c] :
    [c, 0, x]
  )

  return {
    r: Math.round((r + m) * 255),
    g: Math.round((g + m) * 255),
    b: Math.round((b + m) * 255),
  }
}

function rgbToHsl(r, g, b) {
  r /= 255
  g /= 255
  b /= 255

  const max = Math.max(r, g, b)
  const min = Math.min(r, g, b)
  const l = (max + min) / 2
  const d = max - min

  let h = 0
  let s = 0

  if (d !== 0) {
    s = d / (1 - Math.abs(2 * l - 1))

    switch (max) {
      case r: h = ((g - b) / d) % 6; break
      case g: h = (b - r) / d + 2; break
      default: h = (r - g) / d + 4; break
    }

    h = Math.round(h * 60)
    if (h < 0) h += 360
  }

  return [h, Math.round(s * 100), Math.round(l * 100)]
}

function toHex({ r, g, b, a }, includeAlpha) {
  const hex = [r, g, b].map((v) => clamp255(v).toString(16).padStart(2, '0')).join('')

  if (!includeAlpha) return `#${hex}`

  const alphaHex = Math.round(clampAlpha(a) * 255).toString(16).padStart(2, '0')

  return `#${hex}${alphaHex}`
}

export function parseColor(input) {
  if (typeof input !== 'string') return null

  const value = input.trim()
  if (!value) return null

  let m

  if ((m = value.match(HEX_RE))) {
    return hexToRgba(m[1].toLowerCase())
  }

  if ((m = value.match(RGB_RE))) {
    return {
      r: clamp255(m[1]),
      g: clamp255(m[2]),
      b: clamp255(m[3]),
      a: parseAlpha(m[4]),
    }
  }

  if ((m = value.match(HSL_RE))) {
    const { r, g, b } = hslToRgb(parseFloat(m[1]), parseFloat(m[2]), parseFloat(m[3]))

    return { r, g, b, a: parseAlpha(m[4]) }
  }

  return null
}

export function formatColor({ r, g, b, a = 1 }, format = 'hex') {
  const alpha = clampAlpha(a)

  switch (format) {
    case 'hexa':
      return toHex({ r, g, b, a: alpha }, true)
    case 'rgb':
      return `rgb(${clamp255(r)}, ${clamp255(g)}, ${clamp255(b)})`
    case 'rgba':
      return `rgba(${clamp255(r)}, ${clamp255(g)}, ${clamp255(b)}, ${roundAlpha(alpha)})`
    case 'hsl': {
      const [h, s, l] = rgbToHsl(r, g, b)
      return `hsl(${h}, ${s}%, ${l}%)`
    }
    case 'hsla': {
      const [h, s, l] = rgbToHsl(r, g, b)
      return `hsla(${h}, ${s}%, ${l}%, ${roundAlpha(alpha)})`
    }
    case 'hex':
    default:
      return toHex({ r, g, b }, false)
  }
}

export function normalizeColor(input, format = 'hex') {
  const parsed = parseColor(input)

  return parsed ? formatColor(parsed, format) : null
}

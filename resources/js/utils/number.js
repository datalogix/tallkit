import { resolveLocale } from './locale'

export function toNumber(value, fallback = null) {
  const parsed = Number.parseFloat(value)

  return Number.isFinite(parsed) ? parsed : fallback
}

export function clamp(value, min, max) {
  return Math.max(min, Math.min(value, max))
}

export function formatNumber(value, options = {}) {
  return new Intl.NumberFormat(resolveLocale(), options).format(value)
}

import { formatNumber } from './number'

export function formatBytes(bytes, decimals = 1) {
  const units = ['B', 'KB', 'MB', 'GB', 'TB']
  const exponent = bytes > 0 ? Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1) : 0
  const value = bytes > 0 ? bytes / Math.pow(1024, exponent) : 0

  return `${formatNumber(value, { maximumFractionDigits: exponent === 0 ? 0 : decimals })} ${units[exponent]}`
}

export function detectFileType(type, name, fileTypes = {}) {
  if (type?.startsWith('image/')) return 'image'
  if (type?.startsWith('video/')) return 'video'
  if (type?.startsWith('audio/')) return 'audio'

  const extension = name?.includes('.') ? name.split('.').pop().toLowerCase() : ''

  return Object.keys(fileTypes).find((kind) => fileTypes[kind].includes(extension)) ?? 'unknown'
}

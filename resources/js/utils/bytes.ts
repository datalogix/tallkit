export function formatBytes(bytes: number, decimals: number = 1): string {
  if (!bytes) return '0 B'

  const units = ['B', 'KB', 'MB', 'GB', 'TB']
  const exponent = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1)
  const value = bytes / Math.pow(1024, exponent)

  return `${exponent === 0 ? value : value.toFixed(decimals)} ${units[exponent]}`
}

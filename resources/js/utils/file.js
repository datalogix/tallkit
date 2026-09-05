export function formatBytes(bytes, decimals = 1) {
  if (!bytes) return '0 B'

  const units = ['B', 'KB', 'MB', 'GB', 'TB']
  const exponent = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1)
  const value = bytes / Math.pow(1024, exponent)

  return `${exponent === 0 ? value : value.toFixed(decimals)} ${units[exponent]}`
}

export function detectFileType(type, name) {
  if (type.startsWith('image/')) return 'image'
  if (type.startsWith('video/')) return 'video'
  if (type.startsWith('audio/')) return 'audio'

  const extension = name.split('.').pop()?.toLowerCase() ?? ''

  switch (extension) {
    case 'jpg': case 'jpeg': case 'png': case 'gif': return 'image'
    case 'mp4': return 'video'
    case 'mp3': return 'audio'
    case 'pdf': return 'pdf'
    case 'doc': case 'docx': return 'doc'
    case 'xls': case 'xlsx': return 'xls'
    case 'ppt': case 'pptx': return 'ppt'
    case 'rar': case 'zip': case '7z': return 'archive'
    case 'txt': case 'md': return 'text'
    case 'csv': return 'csv'
    case 'json': case 'js': case 'ts': case 'html': case 'css': return 'code'
    default: return 'unknown'
  }
}

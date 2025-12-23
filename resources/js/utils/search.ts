export function levenshtein(a: string, b: string) {
  const alen = a.length, blen = b.length
  if (!alen) return blen
  if (!blen) return alen

  const v0 = new Array(blen + 1).fill(0)
  const v1 = new Array(blen + 1).fill(0)

  for (let j = 0; j <= blen; j++) v0[j] = j

  for (let i = 0; i < alen; i++) {
    v1[0] = i + 1
    for (let j = 0; j < blen; j++) {
      const cost = a[i] === b[j] ? 0 : 1
      v1[j + 1] = Math.min(
        v1[j] + 1,       // insertion
        v0[j + 1] + 1,   // deletion
        v0[j] + cost     // substitution
      )
    }
    for (let j = 0; j <= blen; j++) v0[j] = v1[j]
  }

  return v1[blen]
}

export function fuzzySubsequence(haystack: string, needle: string) {
  if (!needle) return true

  let i = 0, j = 0
  while (i < haystack.length && j < needle.length) {
    if (haystack[i] === needle[j]) j++
    i++
  }
  return j === needle.length
}

export function fuzzyMatch(haystack: string, needle: string, threshold: number = 0.35) {
  if (!needle) return true
  if (haystack.includes(needle)) return true

  if (fuzzySubsequence(haystack, needle)) return true

  const dist = levenshtein(haystack, needle)
  const maxAllowed = Math.max(1, Math.floor(needle.length * threshold))
  return dist <= maxAllowed
}

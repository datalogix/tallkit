// Laravel's app()->getLocale() gives 'pt_BR', an invalid tag to Intl: it throws.
function canonical(locale) {
  try {
    return Intl.getCanonicalLocales(String(locale).replace('_', '-'))[0] ?? null
  } catch {
    return null
  }
}

export function resolveLocale(locale = null) {
  return (locale && canonical(locale))
    || (typeof document !== 'undefined' && document.documentElement.lang && canonical(document.documentElement.lang))
    || (typeof navigator !== 'undefined' && navigator.language && canonical(navigator.language))
    || 'en-US'
}

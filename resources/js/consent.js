import { emit, eventName, storageKey, setStoredText } from './utils'

export function consent(state = 'granted') {
  const value = state === 'granted' ? 'granted' : 'denied'

  window.dataLayer = window.dataLayer || []

  const update = { ad_storage: value, ad_user_data: value, ad_personalization: value, analytics_storage: value }

  // gtag() needs `arguments`, not an array.
  window.gtag = window.gtag || function () { window.dataLayer.push(arguments) }
  window.gtag('consent', 'update', update)

  setStoredText(storageKey('consent'), value)

  emit(document, eventName('consent'), { state: value })
}

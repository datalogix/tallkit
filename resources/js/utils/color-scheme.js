export const isDarkMode = () => document.documentElement.classList.contains('dark')

export function onColorSchemeChange(callback) {
  let dark = isDarkMode()

  const observer = new MutationObserver(() => {
    if (isDarkMode() === dark) return

    dark = isDarkMode()
    callback(dark)
  })

  observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })

  return () => observer.disconnect()
}

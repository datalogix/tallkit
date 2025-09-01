const styles = new Map<string, Promise<Event>>()

export function loadStyle(href: string | string[]): Promise<Event | Event[]> {
  if (Array.isArray(href)) {
    return href.reduce(
      (p, s) => p.then(async (events) => [...events, await loadStyle(s) as Event]),
      Promise.resolve([] as Event[])
    )
  }

  if (styles.has(href)) {
    return styles.get(href)!
  }

  const promise = new Promise<Event>((resolve, reject) => {
    if (document.querySelector(`link[rel="stylesheet"][href="${href}"]`)) {
      resolve(new Event('load'))
      return
    }

    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = href
    link.onload = resolve
    link.onerror = (e) => {
      styles.delete(href)
      reject(e)
    }
    document.head.appendChild(link)
  })

  styles.set(href, promise)
  return promise
}

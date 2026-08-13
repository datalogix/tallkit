const scripts = new Map<string, Promise<Event>>()

export async function loadScript(src: string | string[]): Promise<Event | Event[]>  {
  if (Array.isArray(src)) {
    return src.reduce(
      (p, s) => p.then(async (events) => [...events, await loadScript(s) as Event]),
      Promise.resolve([] as Event[])
    )
  }

  if (scripts.has(src)) {
    return scripts.get(src)!
  }

  const promise = new Promise<Event>((resolve, reject) => {
     if (document.querySelector(`script[src="${src}"]`)) {
      resolve(new Event('load'))
      return
    }

    const script = document.createElement('script')
    script.src = src
    script.defer = true
    script.onload = resolve
    script.onerror = (e) => {
      scripts.delete(src)
      reject(e)
    }
    document.head.appendChild(script)
  })

  scripts.set(src, promise)
  return promise
}

export async function loadRemoteAssets(check: () => boolean, scriptSrc: string | string[], styleHref?: string | string[]) {
  if (check()) {
    return
  }

  await loadScript(scriptSrc)

  if (styleHref) {
    await loadStyle(styleHref)
  }
}

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

const scripts = new Map<string, Promise<Event>>()

export async function loadScript(src: string | string[]) {
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

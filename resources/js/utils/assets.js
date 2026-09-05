const scripts = new Map()

export async function loadScript(src) {
  if (Array.isArray(src)) {
    return src.reduce(
      (p, s) => p.then(async (events) => [...events, await loadScript(s)]),
      Promise.resolve([])
    );
  }

  if (scripts.has(src)) {
    return scripts.get(src);
  }

  const promise = new Promise((resolve, reject) => {
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

export async function loadRemoteAssets(check, scriptSrc, styleHref) {
  if (check()) {
    return
  }

  await loadScript(scriptSrc)

  if (styleHref) {
    await loadStyle(styleHref)
  }
}

const modules = new Map()

export async function loadRemoteModule(src) {
  if (Array.isArray(src)) {
    return Promise.all(src.map((s) => loadRemoteModule(s)))
  }

  if (modules.has(src)) {
    return modules.get(src);
  }

  const promise = import(/* @vite-ignore */ src).catch((e) => {
    modules.delete(src)
    throw e
  })

  modules.set(src, promise)
  return promise
}

const styles = new Map()

export function loadStyle(href) {
  if (Array.isArray(href)) {
    return href.reduce(
      (p, s) => p.then(async (events) => [...events, await loadStyle(s)]),
      Promise.resolve([])
    );
  }

  if (styles.has(href)) {
    return styles.get(href);
  }

  const promise = new Promise((resolve, reject) => {
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

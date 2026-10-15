// Drives headless Chrome over CDP, no dependencies (Node 22+).
// Usage: node browser.mjs <url> <steps.mjs>
// steps.mjs: export default async (page) => { ...; return anything }
// Values stored in page.out are printed even when a later step throws.
import { spawn } from 'node:child_process'
import { mkdtempSync, readFileSync, rmSync, writeFileSync } from 'node:fs'
import { tmpdir } from 'node:os'
import { join, resolve } from 'node:path'
import { pathToFileURL } from 'node:url'

const [url, stepsFile] = process.argv.slice(2)

if (!url || !stepsFile) {
  console.error('Usage: node browser.mjs <url> <steps.mjs>')
  process.exit(2)
}

const KEYS = {
  Tab: [9], Enter: [13, '\r'], Escape: [27], ' ': [32, ' '], Backspace: [8], Delete: [46],
  End: [35], Home: [36], ArrowLeft: [37], ArrowUp: [38], ArrowRight: [39], ArrowDown: [40]
}

const sleep = (ms) => new Promise((done) => setTimeout(done, ms))
const profile = mkdtempSync(join(tmpdir(), 'tallkit-chrome-'))
const chrome = spawn(
  process.env.CHROME_PATH ?? '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
  ['--headless=new', '--remote-debugging-port=0', `--user-data-dir=${profile}`, '--no-first-run', '--window-size=1280,900', 'about:blank'],
  { stdio: 'ignore' }
)

let failed = false
const out = {}

try {
  let port

  for (let i = 0; i < 100 && !port; i++) {
    await sleep(100)

    try {
      port = readFileSync(join(profile, 'DevToolsActivePort'), 'utf8').split('\n')[0]
    } catch {}
  }

  const targets = await (await fetch(`http://127.0.0.1:${port}/json/list`)).json()
  const socket = new WebSocket(targets.find((target) => target.type === 'page').webSocketDebuggerUrl)
  const pending = new Map()
  const waiters = new Map()
  const errors = []
  let lastId = 0

  await new Promise((done) => socket.addEventListener('open', done))

  socket.addEventListener('message', ({ data }) => {
    const message = JSON.parse(data)

    if (message.id) {
      const { done, fail } = pending.get(message.id)

      pending.delete(message.id)

      return message.error ? fail(new Error(message.error.message)) : done(message.result)
    }

    if (message.method === 'Runtime.exceptionThrown') {
      errors.push(message.params.exceptionDetails.exception?.description ?? message.params.exceptionDetails.text)
    }

    if (message.method === 'Runtime.consoleAPICalled' && message.params.type === 'error') {
      errors.push(message.params.args.map((arg) => arg.value ?? arg.description).join(' '))
    }

    waiters.get(message.method)?.()
  })

  const send = (method, params = {}) => new Promise((done, fail) => {
    pending.set(++lastId, { done, fail })
    socket.send(JSON.stringify({ id: lastId, method, params }))
  })

  const page = {
    errors,
    out,

    async eval(expression) {
      const { result, exceptionDetails } = await send('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true })

      if (exceptionDetails) throw new Error(exceptionDetails.exception?.description ?? exceptionDetails.text)

      return result.value
    },

    async goto(target) {
      const loaded = new Promise((done) => waiters.set('Page.loadEventFired', done))

      await send('Page.navigate', { url: target })
      await loaded
      await sleep(500)
    },

    // Real mouse events, so mousedown/focus behave as they do for a user.
    async click(selector) {
      const point = await page.eval(`(() => {
        const el = document.querySelector(${JSON.stringify(selector)})
        if (!el) return null
        el.scrollIntoView({ block: 'center' })
        const rect = el.getBoundingClientRect()
        return { x: rect.x + rect.width / 2, y: rect.y + rect.height / 2, visible: rect.width > 0 && rect.height > 0 }
      })()`)

      if (!point?.visible) throw new Error(`Cannot click ${selector}: ${point ? 'not visible' : 'not found'}`)

      for (const type of ['mouseMoved', 'mousePressed', 'mouseReleased']) {
        await send('Input.dispatchMouseEvent', { type, x: point.x, y: point.y, button: 'left', clickCount: 1 })
      }

      await sleep(100)
    },

    async press(key, { shift = false } = {}) {
      const [code, text] = KEYS[key] ?? []
      const event = { key, code: key === ' ' ? 'Space' : key, windowsVirtualKeyCode: code, modifiers: shift ? 8 : 0 }

      await send('Input.dispatchKeyEvent', { ...event, type: text ? 'keyDown' : 'rawKeyDown', text })
      await send('Input.dispatchKeyEvent', { ...event, type: 'keyUp' })
      await sleep(100)
    },

    async type(text) {
      await send('Input.insertText', { text })
      await sleep(100)
    },

    async screenshot(file) {
      writeFileSync(file, Buffer.from((await send('Page.captureScreenshot')).data, 'base64'))
    },

    async waitFor(expression, timeout = 5000) {
      for (let elapsed = 0; elapsed < timeout; elapsed += 100) {
        if (await page.eval(expression)) return

        await sleep(100)
      }

      throw new Error(`Timed out waiting for ${expression}`)
    },

    // Best time in ms of re-initializing Alpine on the element's subtree.
    measureInit(selector, runs = 7) {
      return page.eval(`(async () => {
        const el = document.querySelector(${JSON.stringify(selector)})
        const html = el.innerHTML
        let best = Infinity

        for (let run = 0; run < ${runs}; run++) {
          Alpine.destroyTree(el)
          el.innerHTML = html

          const start = performance.now()

          Alpine.initTree(el)
          best = Math.min(best, performance.now() - start)

          await new Promise((done) => setTimeout(done, 50))
        }

        return Math.round(best * 10) / 10
      })()`)
    },

    wait: sleep
  }

  await send('Page.enable')
  await send('Runtime.enable')
  await page.goto(url)

  const steps = (await import(pathToFileURL(resolve(stepsFile)).href)).default

  console.log(JSON.stringify({ result: await steps(page), errors }, null, 2))
} catch (error) {
  failed = true
  console.log(JSON.stringify({ out }, null, 2))
  console.error(error.stack ?? error)
} finally {
  chrome.kill()
  await sleep(200)
  rmSync(profile, { recursive: true, force: true })
}

process.exit(failed ? 1 : 0)

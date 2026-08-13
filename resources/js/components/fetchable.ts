import { loadable } from './loadable'

export function fetchable({ url = null, data = null, auto = null, options = {} } = {}) {
  const _loadable = loadable()

  return {
    ..._loadable,

    url: null,
    response: null,
    data: null,
    options: null,
    _controller: null as AbortController | null,

    init () {
      this.clear()

      this.url = url
      this.data = data
      this.options = {
        method: 'get',
        headers: { Accept: 'application/json' },
        responseType: 'json',
        ...options,
      }

      if (this.url && auto !== false) {
        this.fetch()
      }

      if (!this.url && this.data) {
        this.complete()
      }
    },

    async fetch (url = null, options = {}, silent = false) {
      const _url = url || this.url
      const _options = {
        ...(this.options ?? {}),
        ...options,
        headers: { ...(this.options?.headers ?? {}), ...(options.headers ?? {}) },
      }

      this.url = _url
      this.options = _options

      if (!_url) {
        return
      }

      this._controller?.abort()
      const controller = new AbortController()
      this._controller = controller

      this.load(async () => {
        this.response = await window.fetch(_url, { ..._options, signal: controller.signal })

        if (!this.response.ok) {
          throw new Error(this.response.statusText)
        }

        this.data = (_options.responseType
          ? await this.response[_options.responseType]()
          : this.response)
      }, silent)
    },

    reload () {
      return this.fetch()
    },

    update (url = null, options = {}) {
      return this.fetch(url, options, true)
    },

    destroy () {
      _loadable.destroy.call(this)
      this._controller?.abort()
      this._controller = null
    }
  }
}

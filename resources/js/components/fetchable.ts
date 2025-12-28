import { loadable } from './loadable'

export function fetchable(url?: string, data?: {}, autofetch?: Boolean, options?: {}) {
  return {
    ...loadable(),

    url: null,
    response: null,
    data: null,
    options: null,

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

      if (this.url && autofetch !== false) {
        this.fetch()
      }

      if (!this.url && this.data) {
        this.complete()
      }
    },

    async fetch (url = null, options = {}, silent = false) {
      const _url = url || this.url
      const _options = { ...(this.options ?? {}) , ...options }

      this.url = _url
      this.options = _options

      if (!_url) {
        return
      }

      this.load(async () => {
        this.response = await window.fetch(_url, _options)

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
    }
  }
}

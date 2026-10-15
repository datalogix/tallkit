import { queryData, bind, fetchWithRetry, debounce, setFieldValue, createCache, emit, findInField, announce, onFormReset, onLivewireMorphed } from '../utils'

const FILLABLE = ['address', 'neighborhood', 'city', 'state']

function failure(message, name = 'Error') {
  const error = new Error(message)
  error.name = name

  return error
}

export function addressForm({ timeout = 5000, url = null, messages = {}, ...options } = {}) {
  const _cache = createCache('zipcode', { storage: 'session', ...options })

  return {
    abortController: null,
    debouncedSearch: null,
    $els: {},
    blocked: [],
    filled: {},
    loading: false,
    invalid: false,

    init() {
      this.$els = {
        zipcode: queryData(this.$root, 'address-form-zipcode'),
        address: queryData(this.$root, 'address-form-address'),
        number: queryData(this.$root, 'address-form-number'),
        complement: queryData(this.$root, 'address-form-complement'),
        neighborhood: queryData(this.$root, 'address-form-neighborhood'),
        city: queryData(this.$root, 'address-form-city'),
        state: queryData(this.$root, 'address-form-state'),
        error: queryData(this.$root, 'address-form-error'),
      }

      this.$els.loading = findInField(this.$els.address, 'loading')

      const debouncedSearch = this.debouncedSearch = debounce(this.search.bind(this))

      bind(this.$els.zipcode, {
        ['@input']() {
          debouncedSearch(this.$el.value)
        }
      })

      this._stopMorphHook = onLivewireMorphed(({ el }) => {
        if (!el?.contains?.(this.$root)) return

        if (this.loading) this.syncLoading()
        if (this.invalid) this.syncError()
      })

      onFormReset(this.$root, this.$root.closest('form'), () => this.reset())
    },

    setLoading(state) {
      if (state === this.loading) return

      if (state) {
        this.blocked = FILLABLE
          .map((k) => this.$els[k])
          .filter((el) => el && !el.inert)
      }

      this.loading = state
      this.syncLoading()

      if (!state) this.blocked = []
    },

    syncLoading() {
      this.$els.loading?.classList.toggle('hidden', !this.loading)

      this.blocked.forEach((el) => {
        // inert, not disabled: a disabled field is left out of a form submitted meanwhile.
        el.inert = this.loading

        if (this.loading) {
          el.setAttribute('aria-busy', 'true')
        } else {
          el.removeAttribute('aria-busy')
        }
      })
    },

    setError(state) {
      if (state === this.invalid) return

      this.invalid = state
      this.syncError()
    },

    syncError() {
      const { zipcode, error } = this.$els

      error?.toggleAttribute('hidden', !this.invalid)

      if (!zipcode) return

      const describedBy = (zipcode.getAttribute('aria-describedby') ?? '')
        .split(/\s+/)
        .filter((id) => id && id !== error?.id)

      if (this.invalid) {
        zipcode.setAttribute('aria-invalid', 'true')
        if (error?.id) describedBy.push(error.id)
      } else {
        zipcode.removeAttribute('aria-invalid')
      }

      if (describedBy.length) {
        zipcode.setAttribute('aria-describedby', describedBy.join(' '))
      } else {
        zipcode.removeAttribute('aria-describedby')
      }
    },

    focus(el) {
      const active = document.activeElement

      // Only when the user is still here: the lookup can take seconds and they may be typing elsewhere.
      if (active && active !== document.body && active !== this.$els.zipcode) return

      el?.focus()
    },

    resolveState(data) {
      const el = this.$els.state
      if (!el) return ''

      if (el.tagName.toLowerCase() !== 'select') return data.uf ?? data.estado ?? ''

      const options = Array.from(el.options ?? []).map((option) => option.value)

      return [data.uf, data.estado].find((value) => value && options.includes(value)) ?? ''
    },

    async request(input, signal) {
      const controller = new AbortController()
      const abort = () => controller.abort()
      let timedOut = false

      if (signal.aborted) abort()
      signal.addEventListener('abort', abort, { once: true })

      const timer = setTimeout(() => {
        timedOut = true
        abort()
      }, timeout)

      try {
        return await fetch(input, { signal: controller.signal })
      } catch (e) {
        throw timedOut ? failure('Timeout', 'TimeoutError') : e
      } finally {
        clearTimeout(timer)
        signal.removeEventListener('abort', abort)
      }
    },

    async viaCep(zipcode, signal) {
      const res = await this.request(`https://viacep.com.br/ws/${zipcode}/json/`, signal)
      const data = await res.json()

      if (data.erro) throw failure('ViaCEP not found', 'NotFoundError')

      return data
    },

    async brasilApi(zipcode, signal) {
      const res = await this.request(`https://brasilapi.com.br/api/cep/v1/${zipcode}`, signal)

      if (!res.ok) throw failure('BrasilAPI error', res.status === 404 ? 'NotFoundError' : 'Error')

      const data = await res.json()

      return {
        logradouro: data.street,
        bairro: data.neighborhood,
        localidade: data.city,
        uf: data.state
      }
    },

    async custom(zipcode, signal) {
      const res = await this.request(url.replace('{zipcode}', zipcode), signal)

      if (!res.ok) throw failure('Zipcode lookup error', res.status === 404 ? 'NotFoundError' : 'Error')

      const data = await res.json()

      return {
        logradouro: data.address,
        bairro: data.neighborhood,
        localidade: data.city,
        uf: data.state
      }
    },

    async resolveAddress(zipcode, signal) {
      const providers = url ? [this.custom] : [this.viaCep, this.brasilApi]
      let notFound = null
      let last = null

      for (const provider of providers) {
        try {
          return await fetchWithRetry(() => provider.call(this, zipcode, signal))
        } catch (e) {
          if (e.name === 'AbortError') throw e
          if (e.name === 'NotFoundError') notFound = e

          last = e
        }
      }

      throw notFound ?? last
    },

    clearFilled() {
      FILLABLE.forEach((key) => {
        const el = this.$els[key]

        if (el && key in this.filled && el.value === this.filled[key]) setFieldValue(el, '')
      })

      this.filled = {}
    },

    fill(data) {
      const values = {
        address: data.logradouro,
        neighborhood: data.bairro,
        city: data.localidade,
        state: this.resolveState(data),
      }

      this.clearFilled()

      Object.entries(values).forEach(([key, value]) => {
        const el = this.$els[key]

        if (!el || !value) return

        setFieldValue(el, value)
        this.filled[key] = el.value
      })

      announce(messages.filled)
      this.focus(values.address ? this.$els.number : this.$els.address)
    },

    abort() {
      if (!this.abortController) return

      this.abortController.abort()
      this.abortController = null
      this.setLoading(false)
    },

    reset() {
      this.debouncedSearch?.cancel()
      this.abort()
      this.setError(false)
      this.filled = {}
    },

    async search(value) {
      const zipcode = value.replace(/\D/g, '')

      this.abort()
      this.setError(false)

      if (zipcode.length !== 8) return

      emit(this.$root, 'loading', { zipcode })

      const cached = _cache.get(zipcode)
      if (cached) {
        this.fill(cached)
        emit(this.$root, 'loaded', { zipcode, data: cached, cached: true })
        return
      }

      const controller = new AbortController()
      this.abortController = controller

      const { signal } = controller

      this.setLoading(true)
      announce(messages.searching)

      try {
        const data = await this.resolveAddress(zipcode, signal)
        if (signal.aborted) return

        _cache.set(zipcode, data)
        this.setLoading(false)
        this.fill(data)

        emit(this.$root, 'loaded', { zipcode, data, cached: false })
      } catch (e) {
        if (e.name === 'AbortError' || signal.aborted) return

        this.setLoading(false)
        emit(this.$root, 'error', { zipcode, error: e })
        this.clearFilled()

        if (e.name !== 'NotFoundError') {
          announce(messages.failed)
          this.focus(this.$els.address)
          return
        }

        this.setError(true)

        announce(this.$els.error?.textContent.trim())
        this.focus(this.$els.zipcode)
      } finally {
        if (!signal.aborted) this.setLoading(false)
        if (this.abortController === controller) this.abortController = null
      }
    },

    destroy() {
      this.debouncedSearch?.cancel()
      this.abortController?.abort()
      this._stopMorphHook?.()
    }
  }
}

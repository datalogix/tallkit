import { dataKey, bind, fetchWithRetry, debounce, setFieldValue, cache } from '../utils'

export function addressForm(options = {}) {
  const _cache = cache('zipcode', options)

  return {
    abortController: null,
    $els: {},

    init() {
      this.$els = {
        loading: this.$root.querySelector(dataKey('loading')),
        zipcode: this.$root.querySelector(dataKey('address-form-zipcode')),
        address: this.$root.querySelector(dataKey('address-form-address')),
        number: this.$root.querySelector(dataKey('address-form-number')),
        complement: this.$root.querySelector(dataKey('address-form-complement')),
        neighborhood: this.$root.querySelector(dataKey('address-form-neighborhood')),
        city: this.$root.querySelector(dataKey('address-form-city')),
        state: this.$root.querySelector(dataKey('address-form-state')),
      }

      const debouncedSearch = debounce(this.search.bind(this))

      bind(this.$els.zipcode, {
        ['@input']() {
          debouncedSearch(this.$el.value)
        }
      })
    },

    setLoading(state) {
      this.$els.loading?.classList.toggle('hidden', !state)

      ;['address', 'neighborhood', 'city', 'state']
        .map((k) => this.$els[k])
        .filter(Boolean)
        .forEach((el) => el.disabled = state)
    },

    resolveState(data) {
      const el = this.$els.state
      if (!el) return ''

      const value = data.estado ?? data.uf

      if (el.tagName.toLowerCase() === 'input') return value ?? ''

      const hasOption = value != null && Array.from((el).options ?? []).some((option) => option.value === value)

      return hasOption ? value : (data.uf ?? '')
    },

    normalizeZipcode(value) {
      return value.replace(/\D/g, '');
    },

    async viaCep(zipcode, signal) {
      const res = await fetch(`https://viacep.com.br/ws/${zipcode}/json/`, { signal })
      const data = await res.json()

      if (data.erro) {
        const error = new Error('ViaCEP not found')
        error.name = 'NotFoundError'
        throw error
      }

      return data
    },

    async brasilApi(zipcode, signal) {
      const res = await fetch(`https://brasilapi.com.br/api/cep/v1/${zipcode}`, { signal })

      if (!res.ok) {
        const error = new Error('BrasilAPI error')
        if (res.status === 404) error.name = 'NotFoundError'
        throw error
      }

      const data = await res.json()

      return {
        logradouro: data.street,
        bairro: data.neighborhood,
        localidade: data.city,
        uf: data.state
      }
    },

    async resolveAddress(zipcode, signal) {
      const providers = [
        this.viaCep.bind(this),
        this.brasilApi.bind(this)
      ]

      for (const provider of providers) {
        try {
          return await fetchWithRetry(() => provider(zipcode, signal))
        } catch (e) {
          if (e.name === 'AbortError') throw e
        }
      }

      throw new Error('All providers failed')
    },

    fill(data) {
      setFieldValue(this.$els.address, data.logradouro)
      setFieldValue(this.$els.neighborhood, data.bairro)
      setFieldValue(this.$els.city, data.localidade)
      setFieldValue(this.$els.state, this.resolveState(data))

      this.$els.number?.focus()
    },

    async search(value) {
      const zipcode = this.normalizeZipcode(value)
      this.abortController?.abort()

      if (zipcode.length !== 8) return

      const controller = new AbortController()
      this.abortController = controller

      const { signal } = controller

      const cached = _cache.get(zipcode)
      if (cached) {
        this.setLoading(true)
        await new Promise(r => setTimeout(r, 120))
        if (signal.aborted) return

        this.fill(cached)
        this.$dispatch('loaded', { zipcode, data: cached, cached: true })
        this.setLoading(false)
        return
      }

      this.setLoading(true)
      this.$dispatch('loading', { zipcode })

      try {
        const data = await this.resolveAddress(zipcode, signal)
        if (signal.aborted) return

        _cache.set(zipcode, data)
        this.fill(data)

        this.$dispatch('loaded', { zipcode, data, cached: false })
      } catch (e) {
        if (e.name === 'AbortError' || signal.aborted) return

        this.$dispatch('error', { zipcode, error: e })
        this.$els.zipcode?.focus()
      } finally {
        if (!signal.aborted) this.setLoading(false)
      }
    },

    destroy() {
      this.abortController?.abort()
    }
  };
}

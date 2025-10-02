import { bind } from "../utils"

export function addressForm() {
  return {
    get loading() {
      return this.$root.querySelector('[data-tallkit-loading]')
    },

    get zipcode () {
      return this.$root.querySelector('[data-tallkit-address-form-zipcode]')
    },

    get address() {
      return this.$root.querySelector('[data-tallkit-address-form-address]')
    },

    get number() {
      return this.$root.querySelector('[data-tallkit-address-form-number]')
    },

    get complement() {
      return this.$root.querySelector('[data-tallkit-address-form-complement]')
    },

    get neighborhood() {
      return this.$root.querySelector('[data-tallkit-address-form-neighborhood]')
    },

    get city() {
      return this.$root.querySelector('[data-tallkit-address-form-city]')
    },

    get state() {
      return this.$root.querySelector('[data-tallkit-address-form-state]')
    },

    abortController: null,

    init() {
      const that = this

      bind(this.zipcode, {
        ['@keyup']() {
          this.search.bind(that)(this.$el.value)
        }
      })
    },

    async search(value) {
      value = value.replace(/\D/g, '')

      if (value.length < 8) {
        return
      }

      if (this.abortController) {
        this.abortController.abort()
      }

      this.abortController = new AbortController()

      this.loading.style.display = 'block'
      this.address.disabled = true
      this.neighborhood.disabled = true
      this.city.disabled = true
      this.state.disabled = true

      try {
        const res = await fetch(`https://viacep.com.br/ws/${value}/json/`, { signal: this.abortController.signal })
        const data = await res.json()

        if (data.erro) throw new Error('Not found')

        this.address.value = data.logradouro
        this.address.dispatchEvent(new Event('input', { bubbles: true }))
        this.address.dispatchEvent(new Event('change', { bubbles: true }))

        this.neighborhood.value = data.bairro
        this.neighborhood.dispatchEvent(new Event('input', { bubbles: true }))
        this.neighborhood.dispatchEvent(new Event('change', { bubbles: true }))

        this.city.value = data.localidade
        this.city.dispatchEvent(new Event('input', { bubbles: true }))
        this.city.dispatchEvent(new Event('change', { bubbles: true }))

        this.state.value = this.state.tagName.toLowerCase() === 'input' || this.state.querySelector(`option[value="${data.estado}"]`) ? data.estado : data.uf
        this.state.dispatchEvent(new Event('input', { bubbles: true }))
        this.state.dispatchEvent(new Event('change', { bubbles: true }))

        this.number.focus()
      } catch (e) {
        if (e.name === 'AbortError') return

        this.zipcode.focus()
      } finally {
        this.loading.style.display = 'none'
        this.address.disabled = false
        this.neighborhood.disabled = false
        this.city.disabled = false
        this.state.disabled = false
      }
    }
  }
}

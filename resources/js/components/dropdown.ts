import { popover } from "../utils"

export function dropdown() {
  const _popover = popover()

  return {
    ..._popover,

    init() {
      _popover.init.call(this)
      window.Alpine.bind(this.trigger, {
        ['@click']() {
          this.toggle()
        },

        ['@click.outside']() {
          this.close()
        },

        ['@keyup.escape.window']() {
          this.close()
        },
      })
    },
  }
}

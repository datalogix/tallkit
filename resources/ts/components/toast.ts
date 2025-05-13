import { popover } from "../utils"

export function toast () {
  const _popover = popover()

  return {
    ..._popover,

    text: '',
    heading: '',

    open({ text, heading }) {
      this.text = text ?? ''
      this.heading = heading ?? ''
      _popover.open.call(this)
    },
  }
}

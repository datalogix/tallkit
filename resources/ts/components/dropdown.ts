import { toggleable } from "../utils"

export function dropdown() {
  const _toggleable = toggleable()

  return {
    ..._toggleable,
  }
}

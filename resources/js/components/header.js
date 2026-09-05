import { sticky } from '../mixins/sticky'

export function header() {
  return {
    ...sticky()
  }
}

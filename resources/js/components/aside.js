import { sticky } from '../mixins/sticky'

export function aside() {
  return {
    ...sticky()
  }
}

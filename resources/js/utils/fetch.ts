export async function fetchWithRetry(fn: () => Promise<any>, retries: number = 2) {
  try {
    return await fn()
  } catch (e: any) {
    if (retries <= 0 || e.name === 'AbortError' || e.name === 'NotFoundError') throw e
    return fetchWithRetry(fn, retries - 1)
  }
}

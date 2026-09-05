export async function fetchWithRetry(fn, retries = 2) {
  try {
    return await fn()
  } catch (e) {
    if (retries <= 0 || e.name === 'AbortError' || e.name === 'NotFoundError') throw e
    return fetchWithRetry(fn, retries - 1)
  }
}

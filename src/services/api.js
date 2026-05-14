const API_BASE_URL = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:80').replace(/\/$/, '')

async function apiRequest(path, options = {}) {
  const { body, headers, ...rest } = options
  const response = await fetch(`${API_BASE_URL}${path}`, {
    headers: {
      Accept: 'application/json',
      ...(body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
      ...headers,
    },
    body: body instanceof FormData ? body : body ? JSON.stringify(body) : undefined,
    ...rest,
  })

  const contentType = response.headers.get('content-type') || ''
  const payload = contentType.includes('application/json') ? await response.json() : await response.text()

  if (!response.ok) {
    const message = typeof payload === 'object' && payload?.message ? payload.message : 'Request failed'
    throw new Error(message)
  }

  return payload
}

export const api = {
  baseUrl: API_BASE_URL,
  login: (credentials) => apiRequest('/api/auth/login.php', { method: 'POST', body: credentials }),
  register: (account) => apiRequest('/api/auth/register.php', { method: 'POST', body: account }),
  createRescueReport: (report) => apiRequest('/api/reports/create.php', { method: 'POST', body: report }),
  createAdoptionRequest: (request) =>
    apiRequest('/api/adoptions/request.php', { method: 'POST', body: request }),
  createCommunityPost: (post) => apiRequest('/api/community/create.php', { method: 'POST', body: post }),
  addToCart: (product) => apiRequest('/api/shop/cart.php', { method: 'POST', body: product }),
  updateVolunteerAvailability: (availability) =>
    apiRequest('/api/volunteers/availability.php', { method: 'POST', body: availability }),
}

export { apiRequest }

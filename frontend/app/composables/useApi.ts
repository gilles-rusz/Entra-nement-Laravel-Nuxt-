// Toutes les requêtes vers l'API Laravel passent par ici.
// Le token reçu au login est stocké dans un cookie et ajouté automatiquement à chaque requête.
export function useToken() {
  return useCookie<string | null>('token', { sameSite: 'strict' })
}

export function useApi() {
  const config = useRuntimeConfig()
  const token = useToken()

  return $fetch.create({
    baseURL: config.public.apiBase,
    headers: { Accept: 'application/json' },
    onRequest({ options }) {
      if (token.value) {
        options.headers.set('Authorization', `Bearer ${token.value}`)
      }
    },
    onResponseError({ response }) {
      if (response.status === 401) {
        token.value = null
        navigateTo('/')
      }
    },
  })
}

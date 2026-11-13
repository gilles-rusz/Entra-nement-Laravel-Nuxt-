// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  // Le front tourne uniquement dans le navigateur (comme une SPA) : plus simple pour débuter.
  ssr: false,
  runtimeConfig: {
    public: {
      apiBase: '/api/v1',
    },
  },
  // Nuxt transmet les appels /api/... à Laravel (port 8000) : le navigateur ne parle qu'à Nuxt,
  // donc pas de souci de CORS.
  routeRules: {
    '/api/**': { proxy: 'http://localhost:8000/api/**' },
  },
  app: {
    head: { title: 'Entraînement Laravel + Nuxt', htmlAttrs: { lang: 'fr' } },
  },
})

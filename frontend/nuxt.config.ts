// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  // Le front tourne uniquement dans le navigateur (comme une SPA) : plus simple pour débuter.
  ssr: false,
  runtimeConfig: {
    public: {
      // Adresse de l'API Laravel, modifiable avec la variable NUXT_PUBLIC_API_BASE.
      apiBase: 'http://localhost:8000/api/v1',
    },
  },
  app: {
    head: { title: 'Entraînement Laravel + Nuxt', htmlAttrs: { lang: 'fr' } },
  },
})

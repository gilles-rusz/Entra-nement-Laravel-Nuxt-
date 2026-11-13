<script setup lang="ts">
const token = useToken()
const api = useApi()

async function deconnexion() {
  await api('/logout', { method: 'POST' }).catch(() => {})
  token.value = null
  navigateTo('/')
}
</script>

<template>
  <div class="page">
    <header>
      <strong>Pneus B2B, entraînement</strong>
      <nav v-if="token">
        <NuxtLink to="/produits">Produits</NuxtLink>
        <NuxtLink to="/import">Import CSV</NuxtLink>
        <button class="lien" @click="deconnexion">Déconnexion</button>
      </nav>
    </header>
    <main>
      <NuxtPage />
    </main>
  </div>
</template>

<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: system-ui, sans-serif; background: #f4f5f7; color: #1f2937; }
.page { max-width: 960px; margin: 0 auto; padding: 1rem; }
header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #d1d5db; margin-bottom: 1.5rem; }
nav { display: flex; gap: 1rem; align-items: center; }
nav a { color: #1d4ed8; text-decoration: none; }
nav a.router-link-active { font-weight: bold; }
.carte { background: white; border-radius: 8px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); margin-bottom: 1rem; }
label { display: block; margin-bottom: .75rem; font-size: .9rem; }
input, select { display: block; width: 100%; padding: .5rem; margin-top: .25rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; }
button { background: #1d4ed8; color: white; border: 0; border-radius: 6px; padding: .55rem 1rem; font-size: 1rem; cursor: pointer; }
button:disabled { opacity: .5; cursor: not-allowed; }
button.lien { background: none; color: #1d4ed8; padding: 0; }
table { width: 100%; border-collapse: collapse; background: white; }
th, td { text-align: left; padding: .5rem; border-bottom: 1px solid #e5e7eb; font-size: .9rem; }
.erreur { color: #b91c1c; }
.succes { color: #15803d; }
.filtres { display: flex; gap: 1rem; }
.filtres label { flex: 1; }
</style>

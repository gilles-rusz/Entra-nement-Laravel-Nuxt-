<script setup lang="ts">
const api = useApi()
const token = useToken()

const email = ref('garage@exemple.fr')
const password = ref('')
const erreur = ref('')

if (token.value) navigateTo('/produits')

async function connexion() {
  erreur.value = ''
  try {
    const reponse = await api<{ token: string }>('/login', {
      method: 'POST',
      body: { email: email.value, password: password.value },
    })
    token.value = reponse.token
    navigateTo('/produits')
  } catch (e: any) {
    erreur.value = e?.data?.message ?? 'Connexion impossible.'
  }
}
</script>

<template>
  <div class="carte" style="max-width: 400px; margin: 0 auto">
    <h1>Connexion client</h1>
    <form @submit.prevent="connexion">
      <label>Email <input v-model="email" type="email" required></label>
      <label>Mot de passe <input v-model="password" type="password" required></label>
      <button type="submit">Se connecter</button>
      <p v-if="erreur" class="erreur">{{ erreur }}</p>
    </form>
    <p><small>Compte de démo : garage@exemple.fr / motdepasse</small></p>
  </div>
</template>

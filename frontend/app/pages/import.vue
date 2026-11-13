<script setup lang="ts">
interface Rapport {
  statut: 'ok' | 'partiel' | 'refuse'
  message: string
  crees: number
  mis_a_jour: number
  erreurs: { ligne: number; message: string }[]
}

const api = useApi()
const fichier = ref<File | null>(null)
const rapport = ref<Rapport | null>(null)
const erreur = ref('')

function choisir(event: Event) {
  fichier.value = (event.target as HTMLInputElement).files?.[0] ?? null
}

async function envoyer() {
  if (!fichier.value) return
  rapport.value = null
  erreur.value = ''

  // Un fichier s'envoie en multipart/form-data, pas en JSON.
  const formulaire = new FormData()
  formulaire.append('fichier', fichier.value)

  try {
    rapport.value = await api<Rapport>('/imports/produits', { method: 'POST', body: formulaire })
  } catch (e: any) {
    if (e?.data?.statut) rapport.value = e.data
    else erreur.value = e?.data?.message ?? 'Erreur pendant l\'envoi.'
  }
}
</script>

<template>
  <div>
    <h1>Import CSV des produits</h1>
    <div class="carte">
      <p>Le même fichier que celui déposé sur le FTP, envoyé ici en HTTPS. Exemples dans le dossier <code>exemples/</code>.</p>
      <input type="file" accept=".csv,text/csv" @change="choisir">
      <p><button :disabled="!fichier" @click="envoyer">Envoyer</button></p>
      <p v-if="erreur" class="erreur">{{ erreur }}</p>
    </div>

    <div v-if="rapport" class="carte">
      <p :class="rapport.statut === 'ok' ? 'succes' : 'erreur'"><strong>{{ rapport.message }}</strong></p>
      <p>Créés : {{ rapport.crees }}, mis à jour : {{ rapport.mis_a_jour }}</p>
      <table v-if="rapport.erreurs.length">
        <thead><tr><th>Ligne</th><th>Problème</th></tr></thead>
        <tbody>
          <tr v-for="e in rapport.erreurs" :key="e.ligne"><td>{{ e.ligne }}</td><td>{{ e.message }}</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

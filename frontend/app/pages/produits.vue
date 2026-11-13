<script setup lang="ts">
interface Produit {
  id: number
  reference: string
  marque: string
  modele: string
  dimension: string
  saison: 'ete' | 'hiver' | '4saisons'
  prix_ht: number
  stock: number
  disponible: boolean
}

const api = useApi()
const marque = ref('')
const saison = ref('')
const produits = ref<Produit[]>([])
const quantites = reactive<Record<number, number>>({})
const message = ref('')
const erreur = ref('')

async function charger() {
  const query: Record<string, string> = {}
  if (marque.value) query.marque = marque.value
  if (saison.value) query.saison = saison.value
  const reponse = await api<{ data: Produit[] }>('/produits', { query })
  produits.value = reponse.data
}

// Relance la recherche dès qu'un filtre change.
watch([marque, saison], charger)
onMounted(charger)

const panier = computed(() =>
  Object.entries(quantites)
    .filter(([, quantite]) => quantite > 0)
    .map(([id, quantite]) => ({ produit_id: Number(id), quantite })),
)

async function commander() {
  message.value = ''
  erreur.value = ''
  try {
    const reponse = await api<{ data: { id: number; total_ht: number } }>('/commandes', {
      method: 'POST',
      body: { lignes: panier.value },
    })
    message.value = `Commande n°${reponse.data.id} enregistrée, total ${reponse.data.total_ht.toFixed(2)} € HT.`
    Object.keys(quantites).forEach((id) => delete quantites[Number(id)])
    await charger()
  } catch (e: any) {
    const erreurs = e?.data?.errors
    erreur.value = erreurs ? Object.values(erreurs).flat().join(' ') : (e?.data?.message ?? 'Erreur.')
  }
}

const libelleSaison = { ete: 'Été', hiver: 'Hiver', '4saisons': '4 saisons' }
</script>

<template>
  <div>
    <h1>Catalogue</h1>
    <div class="carte filtres">
      <label>Marque
        <select v-model="marque">
          <option value="">Toutes</option>
          <option>Michelin</option>
          <option>Continental</option>
          <option>Bridgestone</option>
          <option>Pirelli</option>
          <option>Goodyear</option>
        </select>
      </label>
      <label>Saison
        <select v-model="saison">
          <option value="">Toutes</option>
          <option value="ete">Été</option>
          <option value="hiver">Hiver</option>
          <option value="4saisons">4 saisons</option>
        </select>
      </label>
    </div>

    <table>
      <thead>
        <tr><th>Référence</th><th>Pneu</th><th>Dimension</th><th>Saison</th><th>Prix HT</th><th>Stock</th><th>Quantité</th></tr>
      </thead>
      <tbody>
        <tr v-for="p in produits" :key="p.id">
          <td>{{ p.reference }}</td>
          <td>{{ p.marque }} {{ p.modele }}</td>
          <td>{{ p.dimension }}</td>
          <td>{{ libelleSaison[p.saison] }}</td>
          <td>{{ p.prix_ht.toFixed(2) }} €</td>
          <td :class="{ erreur: !p.disponible }">{{ p.stock }}</td>
          <td><input v-model.number="quantites[p.id]" type="number" min="0" style="width: 80px" :disabled="!p.disponible"></td>
        </tr>
      </tbody>
    </table>

    <div class="carte" style="margin-top: 1rem">
      <button :disabled="panier.length === 0" @click="commander">Commander ({{ panier.length }} ligne(s))</button>
      <p v-if="message" class="succes">{{ message }}</p>
      <p v-if="erreur" class="erreur">{{ erreur }}</p>
    </div>
  </div>
</template>

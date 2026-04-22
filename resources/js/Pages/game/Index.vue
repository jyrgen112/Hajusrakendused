<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto p-6">

      <!-- Header -->
      <div class="flex items-center gap-4 mb-8">
        <a href="/" class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm transition">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
          </svg>
          Back to Home
        </a>
        <h1 class="text-3xl font-bold">🎮 My Favorite Games</h1>
      </div>

      <!-- Add Game Form -->
      <div class="bg-gray-800 rounded-2xl p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">{{ editingGame ? 'Edit Game' : 'Add New Game' }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <input v-model="form.title" type="text" placeholder="Title" class="bg-gray-700 rounded-xl px-4 py-2 text-white placeholder-gray-400 outline-none" />
          <input v-model="form.image" type="url" placeholder="Image URL" class="bg-gray-700 rounded-xl px-4 py-2 text-white placeholder-gray-400 outline-none" />
          <input v-model="form.genre" type="text" placeholder="Genre (e.g. RPG, FPS)" class="bg-gray-700 rounded-xl px-4 py-2 text-white placeholder-gray-400 outline-none" />
          <input v-model="form.release_year" type="number" placeholder="Release Year" class="bg-gray-700 rounded-xl px-4 py-2 text-white placeholder-gray-400 outline-none" />
          <textarea v-model="form.description" placeholder="Description" class="bg-gray-700 rounded-xl px-4 py-2 text-white placeholder-gray-400 outline-none sm:col-span-2" rows="3"></textarea>
        </div>
        <div class="flex gap-3 mt-4">
          <button @click="submitForm" class="bg-indigo-600 hover:bg-indigo-700 px-6 py-2 rounded-xl font-semibold transition">
            {{ editingGame ? 'Update' : 'Add Game' }}
          </button>
          <button v-if="editingGame" @click="cancelEdit" class="bg-gray-600 hover:bg-gray-500 px-6 py-2 rounded-xl font-semibold transition">
            Cancel
          </button>
        </div>
      </div>

      <!-- Search & Filter -->
      <div class="flex flex-wrap gap-4 mb-6">
        <input v-model="search" type="text" placeholder="Search games..." class="bg-gray-800 rounded-xl px-4 py-2 text-white placeholder-gray-400 outline-none flex-1 min-w-48" />
        <select v-model="selectedGenre" class="bg-gray-800 rounded-xl px-4 py-2 text-white outline-none">
          <option value="">All Genres</option>
          <option v-for="genre in genres" :key="genre" :value="genre">{{ genre }}</option>
        </select>
        <select v-model="sortBy" class="bg-gray-800 rounded-xl px-4 py-2 text-white outline-none">
          <option value="newest">Newest Added</option>
          <option value="year">By Release Year</option>
        </select>
      </div>

      <!-- Games Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="game in filteredGames" :key="game.id" class="bg-gray-800 rounded-2xl overflow-hidden hover:ring-2 hover:ring-indigo-500 transition">
          <img v-if="game.image" :src="game.image" :alt="game.title" class="w-full h-48 object-cover" />
          <div v-else class="w-full h-48 bg-gray-700 flex items-center justify-center text-5xl">🎮</div>
          <div class="p-5">
            <div class="flex justify-between items-start mb-2">
              <h2 class="text-lg font-bold">{{ game.title }}</h2>
              <span class="text-xs bg-indigo-600 px-2 py-1 rounded-full">{{ game.genre }}</span>
            </div>
            <p class="text-gray-400 text-sm mb-2">{{ game.release_year }}</p>
            <p class="text-gray-300 text-sm leading-relaxed">{{ game.description }}</p>
            <div class="flex gap-3 mt-4">
              <button @click="startEdit(game)" class="text-indigo-400 text-sm hover:underline">Edit</button>
              <button @click="deleteGame(game.id)" class="text-red-400 text-sm hover:underline">Delete</button>
            </div>
          </div>
        </div>
        <p v-if="filteredGames.length === 0" class="text-gray-400 col-span-3 text-center py-12">No games found!</p>
      </div>

      <!-- API Info -->
      <div class="mt-12 bg-gray-800 rounded-2xl p-6">
        <h2 class="text-lg font-semibold mb-3">📡 JSON API</h2>
        <p class="text-gray-400 text-sm mb-3">Access the games data via API:</p>
        <div class="space-y-2 text-sm font-mono bg-gray-900 rounded-xl p-4">
          <p class="text-green-400">GET /api/games</p>
          <p class="text-green-400">GET /api/games?search=zelda</p>
          <p class="text-green-400">GET /api/games?genre=RPG</p>
          <p class="text-green-400">GET /api/games?limit=5</p>
          <p class="text-green-400">GET /api/games?sort=year</p>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  games: Array,
})

const form = ref({ title: '', image: '', description: '', genre: '', release_year: '' })
const editingGame = ref(null)
const search = ref('')
const selectedGenre = ref('')
const sortBy = ref('newest')

const genres = computed(() => [...new Set(props.games.map(g => g.genre))].sort())

const filteredGames = computed(() => {
  let result = [...props.games]

  if (search.value) {
    result = result.filter(g => g.title.toLowerCase().includes(search.value.toLowerCase()))
  }

  if (selectedGenre.value) {
    result = result.filter(g => g.genre === selectedGenre.value)
  }

  if (sortBy.value === 'year') {
    result.sort((a, b) => b.release_year - a.release_year)
  }

  return result
})

function submitForm() {
  if (editingGame.value) {
    router.put(`/games/${editingGame.value.id}`, form.value, {
      onSuccess: () => cancelEdit()
    })
  } else {
    router.post('/games', form.value, {
      onSuccess: () => { form.value = { title: '', image: '', description: '', genre: '', release_year: '' } }
    })
  }
}

function startEdit(game) {
  editingGame.value = game
  form.value = { ...game }
}

function cancelEdit() {
  editingGame.value = null
  form.value = { title: '', image: '', description: '', genre: '', release_year: '' }
}

function deleteGame(id) {
  if (confirm('Delete this game?')) {
    router.delete(`/games/${id}`)
  }
}
</script>
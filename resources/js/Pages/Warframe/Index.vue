<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto p-6">

      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-4xl font-bold text-blue-400">⚔️ Warframe Alerts</h1>
          <p class="text-gray-400 mt-1">Live alerts, events and invasions</p>
        </div>
        <button @click="refresh" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-semibold">
          🔄 Refresh
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-gray-800 rounded-2xl p-5 mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
          <label class="text-xs text-gray-400 mb-1 block">Search</label>
          <input
            v-model="localFilters.search"
            @input="applyFilters"
            type="text"
            placeholder="Search..."
            class="w-full bg-gray-700 rounded-xl px-3 py-2 text-sm text-white outline-none"
          />
        </div>
        <div>
          <label class="text-xs text-gray-400 mb-1 block">Type</label>
          <select v-model="localFilters.type" @change="applyFilters" class="w-full bg-gray-700 rounded-xl px-3 py-2 text-sm text-white outline-none">
            <option value="all">All Types</option>
            <option value="alert">Alerts</option>
            <option value="event">Events</option>
            <option value="invasion">Invasions</option>
          </select>
        </div>
        <div>
          <label class="text-xs text-gray-400 mb-1 block">Sort</label>
          <select v-model="localFilters.sort" @change="applyFilters" class="w-full bg-gray-700 rounded-xl px-3 py-2 text-sm text-white outline-none">
            <option value="latest">Latest</option>
            <option value="oldest">Oldest</option>
            <option value="type">By Type</option>
          </select>
        </div>
        <div>
          <label class="text-xs text-gray-400 mb-1 block">Limit</label>
          <select v-model="localFilters.limit" @change="applyFilters" class="w-full bg-gray-700 rounded-xl px-3 py-2 text-sm text-white outline-none">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
          </select>
        </div>
      </div>

      <!-- No alerts -->
      <div v-if="alerts.length === 0" class="text-center py-20">
        <p class="text-gray-400 text-xl">No active alerts found.</p>
        <button @click="refresh" class="mt-4 text-blue-400 hover:underline">Click to fetch latest data</button>
      </div>

      <!-- Alerts Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="alert in alerts"
          :key="alert.id"
          class="bg-gray-800 rounded-2xl p-5 border border-gray-700 hover:border-blue-500 transition"
        >
          <!-- Type Badge -->
          <div class="flex justify-between items-start mb-3">
            <span
              class="text-xs font-bold px-3 py-1 rounded-full"
              :class="{
                'bg-red-500/20 text-red-400': alert.type === 'alert',
                'bg-blue-500/20 text-blue-400': alert.type === 'event',
                'bg-yellow-500/20 text-yellow-400': alert.type === 'invasion',
              }"
            >
              {{ alert.type.toUpperCase() }}
            </span>
            <span v-if="alert.eta" class="text-xs text-gray-400">⏱ {{ alert.eta }}</span>
          </div>

          <!-- Title & Description -->
          <h3 class="text-lg font-bold text-white mb-1">{{ alert.title }}</h3>
          <p class="text-gray-400 text-sm mb-3">{{ alert.description }}</p>

          <!-- Faction -->
          <div v-if="alert.faction" class="flex items-center gap-2">
            <span class="text-xs text-gray-500">Faction:</span>
            <span class="text-xs font-semibold text-blue-300">{{ alert.faction }}</span>
          </div>
        </div>
      </div>

      <!-- API Info -->
      <div class="mt-10 bg-gray-800 rounded-2xl p-5 border border-gray-700">
        <h2 class="text-lg font-semibold mb-2 text-blue-400">📡 JSON API</h2>
        <p class="text-gray-400 text-sm mb-3">Access the raw data via the API endpoint with filtering and sorting support:</p>
        <div class="space-y-2 text-sm font-mono text-green-400">
          <p>/api/warframe</p>
          <p>/api/warframe?type=alert</p>
          <p>/api/warframe?type=invasion&faction=Grineer</p>
          <p>/api/warframe?search=Ceres&sort=latest&limit=10</p>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  alerts: Array,
  filters: Object,
})

const localFilters = ref({ ...props.filters })

function applyFilters() {
  router.get('/warframe', localFilters.value, { preserveState: true, replace: true })
}

function refresh() {
  router.post('/warframe/refresh')
}
</script>
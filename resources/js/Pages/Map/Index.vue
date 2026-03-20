<template>
  <div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto p-6">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">🗺️ Map & Markers</h1>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div id="map" style="width: 100%; height: 500px;"></div>
          </div>
          <p class="text-sm text-gray-500 mt-2">💡 Click on the map to add a new marker</p>
        </div>
        <div class="space-y-4">
          <div class="bg-white rounded-2xl shadow p-5">
            <h2 class="text-lg font-semibold mb-4">{{ editingMarker ? 'Edit Marker' : 'Add Marker' }}</h2>
            <div class="space-y-3">
              <input v-model="form.name" type="text" placeholder="Name" class="w-full border rounded-xl px-3 py-2 text-sm" />
              <input v-model="form.latitude" type="number" step="any" placeholder="Latitude" class="w-full border rounded-xl px-3 py-2 text-sm" />
              <input v-model="form.longitude" type="number" step="any" placeholder="Longitude" class="w-full border rounded-xl px-3 py-2 text-sm" />
              <textarea v-model="form.description" placeholder="Description" class="w-full border rounded-xl px-3 py-2 text-sm" rows="3"></textarea>
              <div class="flex gap-2">
                <button @click="submitForm" class="flex-1 bg-indigo-600 text-white rounded-xl py-2 text-sm font-semibold hover:bg-indigo-700">
                  {{ editingMarker ? 'Update' : 'Add Marker' }}
                </button>
                <button v-if="editingMarker" @click="cancelEdit" class="flex-1 bg-gray-200 text-gray-700 rounded-xl py-2 text-sm font-semibold hover:bg-gray-300">
                  Cancel
                </button>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-2xl shadow p-5">
            <h2 class="text-lg font-semibold mb-4">Markers ({{ markers.length }})</h2>
            <div class="space-y-3 max-h-64 overflow-y-auto">
              <div v-for="marker in markers" :key="marker.id" class="border rounded-xl p-3 text-sm">
                <p class="font-semibold">{{ marker.name }}</p>
                <p class="text-gray-500 text-xs">{{ marker.description }}</p>
                <p class="text-gray-400 text-xs mt-1">{{ marker.latitude }}, {{ marker.longitude }}</p>
                <div class="flex gap-2 mt-2">
                  <button @click="startEdit(marker)" class="text-indigo-600 hover:underline text-xs">Edit</button>
                  <button @click="deleteMarker(marker.id)" class="text-red-500 hover:underline text-xs">Delete</button>
                </div>
              </div>
              <p v-if="markers.length === 0" class="text-gray-400 text-sm">No markers yet. Click the map to add one!</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  markers: Array,
})

const form = ref({ name: '', latitude: '', longitude: '', description: '' })
const editingMarker = ref(null)
let map = null
let leafletMarkers = []

onMounted(() => {
  const link = document.createElement('link')
  link.rel = 'stylesheet'
  link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
  document.head.appendChild(link)

  const script = document.createElement('script')
  script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
  script.onload = () => setTimeout(() => initMap(), 100)
  document.head.appendChild(script)
})

watch(() => props.markers, () => {
  if (map) refreshMarkers()
}, { deep: true })

function initMap() {
  map = L.map('map').setView([58.5953, 25.0136], 7)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map)
  refreshMarkers()
  map.on('click', (e) => {
    form.value.latitude = e.latlng.lat.toFixed(7)
    form.value.longitude = e.latlng.lng.toFixed(7)
  })
}

function refreshMarkers() {
  leafletMarkers.forEach(m => m.remove())
  leafletMarkers = []
  props.markers.forEach(m => {
    const marker = L.marker([parseFloat(m.latitude), parseFloat(m.longitude)])
      .addTo(map)
      .bindPopup(`<b>${m.name}</b><br>${m.description || ''}`)
    leafletMarkers.push(marker)
  })
}

function submitForm() {
  if (editingMarker.value) {
    router.put(`/markers/${editingMarker.value.id}`, form.value, {
      onSuccess: () => cancelEdit()
    })
  } else {
    router.post('/markers', form.value, {
      onSuccess: () => { form.value = { name: '', latitude: '', longitude: '', description: '' } }
    })
  }
}

function startEdit(marker) {
  editingMarker.value = marker
  form.value = { ...marker }
}

function cancelEdit() {
  editingMarker.value = null
  form.value = { name: '', latitude: '', longitude: '', description: '' }
}

function deleteMarker(id) {
  if (confirm('Delete this marker?')) {
    router.delete(`/markers/${id}`)
  }
}
</script>
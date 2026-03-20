<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center p-6">
    <div class="bg-white/20 backdrop-blur-md rounded-3xl p-8 w-full max-w-md text-white shadow-2xl">
      
      <!-- Search -->
      <form @submit.prevent="search" class="flex gap-2 mb-8">
        <input
          v-model="cityInput"
          type="text"
          placeholder="Search city..."
          class="flex-1 rounded-xl px-4 py-2 text-gray-800 outline-none"
        />
        <button type="submit" class="bg-white text-indigo-600 font-bold px-4 py-2 rounded-xl hover:bg-indigo-100">
          Search
        </button>
      </form>

      <!-- Error -->
      <div v-if="weather.cod !== 200 && weather.cod !== undefined" class="text-center text-red-200 text-lg">
        City not found. Try again!
      </div>

      <!-- Weather Data -->
      <div v-else-if="weather.main" class="text-center">
        <h1 class="text-4xl font-bold mb-1">{{ weather.name }}, {{ weather.sys.country }}</h1>
        <img
          :src="`https://openweathermap.org/img/wn/${weather.weather[0].icon}@2x.png`"
          :alt="weather.weather[0].description"
          class="mx-auto w-24 h-24"
        />
        <p class="text-6xl font-extrabold">{{ Math.round(weather.main.temp) }}°C</p>
        <p class="text-xl capitalize mt-1">{{ weather.weather[0].description }}</p>

        <div class="grid grid-cols-2 gap-4 mt-8 text-sm">
          <div class="bg-white/20 rounded-2xl p-4">
            <p class="opacity-70">Feels like</p>
            <p class="text-2xl font-bold">{{ Math.round(weather.main.feels_like) }}°C</p>
          </div>
          <div class="bg-white/20 rounded-2xl p-4">
            <p class="opacity-70">Humidity</p>
            <p class="text-2xl font-bold">{{ weather.main.humidity }}%</p>
          </div>
          <div class="bg-white/20 rounded-2xl p-4">
            <p class="opacity-70">Wind</p>
            <p class="text-2xl font-bold">{{ weather.wind.speed }} m/s</p>
          </div>
          <div class="bg-white/20 rounded-2xl p-4">
            <p class="opacity-70">Pressure</p>
            <p class="text-2xl font-bold">{{ weather.main.pressure }} hPa</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  weather: Object,
  city: String,
})

const cityInput = ref(props.city)

function search() {
  router.get('/weather', { city: cityInput.value })
}
</script>   
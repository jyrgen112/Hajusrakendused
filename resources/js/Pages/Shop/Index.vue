<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto p-6">
      <div class="flex justify-between items-center mb-8">

        <!-- Back Button -->
        <a href="/" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-800 text-sm transition">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
          </svg>
          Back to Home
        </a>

        <h1 class="text-3xl font-bold text-gray-800">🛍️ Shop</h1>
        <a href="/cart" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-semibold hover:bg-indigo-700">
          🛒 Cart
        </a>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="product in products" :key="product.id" class="bg-white rounded-2xl shadow overflow-hidden">
          <img :src="product.image" :alt="product.name" class="w-full h-48 object-cover" />
          <div class="p-5">
            <h2 class="text-lg font-bold text-gray-800">{{ product.name }}</h2>
            <p class="text-gray-500 text-sm mt-1 mb-3">{{ product.description }}</p>
            <div class="flex items-center justify-between">
              <span class="text-2xl font-bold text-indigo-600">€{{ product.price }}</span>
              <div class="flex items-center gap-2">
                <input v-model="quantities[product.id]" type="number" min="1" max="10" class="w-16 border rounded-xl px-2 py-1 text-sm text-center" />
                <button @click="addToCart(product)" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-700">
                  Add
                </button>
              </div>
            </div>
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
  products: Array,
})

const quantities = ref({})
props.products.forEach(p => { quantities.value[p.id] = 1 })

function addToCart(product) {
  router.post('/cart/add', {
    product_id: product.id,
    quantity: quantities.value[product.id] || 1
  }, { onSuccess: () => alert('Added to cart!') })
}
</script>
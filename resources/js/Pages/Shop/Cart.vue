<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto p-6">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">🛒 Your Cart</h1>

      <div v-if="cartItems.length === 0" class="text-center py-20 text-gray-400">
        <p class="text-xl">Your cart is empty.</p>
        <a href="/shop" class="mt-4 inline-block text-indigo-600 hover:underline">Continue shopping</a>
      </div>

      <div v-else>
        <div class="bg-white rounded-2xl shadow overflow-hidden mb-6">
          <div v-for="item in cartItems" :key="item.id" class="flex items-center gap-4 p-4 border-b last:border-0">
            <img :src="item.image" :alt="item.name" class="w-20 h-20 object-cover rounded-xl" />
            <div class="flex-1">
              <h3 class="font-semibold text-gray-800">{{ item.name }}</h3>
              <p class="text-indigo-600 font-bold">€{{ item.price }}</p>
            </div>
            <div class="flex items-center gap-2">
              <input
                :value="item.quantity"
                @change="updateCart(item.id, $event.target.value)"
                type="number" min="1" max="10"
                class="w-16 border rounded-xl px-2 py-1 text-sm text-center"
              />
              <button @click="removeFromCart(item.id)" class="text-red-500 hover:text-red-700 text-sm font-semibold">
                Remove
              </button>
            </div>
            <p class="w-20 text-right font-bold text-gray-700">€{{ (item.price * item.quantity).toFixed(2) }}</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6 flex justify-between items-center">
          <div>
            <p class="text-gray-500 text-sm">Total</p>
            <p class="text-3xl font-bold text-indigo-600">€{{ total.toFixed(2) }}</p>
          </div>
          <div class="flex gap-3">
            <a href="/shop" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300">
              Continue Shopping
            </a>
            <a href="/checkout" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700">
              Checkout
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  cartItems: Array,
})

const total = computed(() => {
  return props.cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0)
})

function updateCart(productId, quantity) {
  router.post('/cart/update', { product_id: productId, quantity: parseInt(quantity) })
}

function removeFromCart(productId) {
  router.post('/cart/remove', { product_id: productId })
}
</script>
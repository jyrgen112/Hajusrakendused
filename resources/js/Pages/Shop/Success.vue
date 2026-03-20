<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow p-12 text-center max-w-md">
      <div class="text-6xl mb-4">🎉</div>
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Order Confirmed!</h1>
      <p class="text-gray-500 mb-2">Thank you, {{ order.first_name }}!</p>
      <p class="text-gray-400 text-sm mb-6">A confirmation will be sent to {{ order.email }}</p>
      <p class="text-2xl font-bold text-indigo-600 mb-8">Total paid: €{{ order.total }}</p>
      <a href="/shop" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700">
        Continue Shopping
      </a>
    </div>
  </div>
</template>

<script setup>
defineProps({ order: Object })
</script>
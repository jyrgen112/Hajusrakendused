<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto p-6">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">💳 Checkout</h1>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Order Summary -->
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
          <div v-for="item in cartItems" :key="item.id" class="flex justify-between items-center py-2 border-b last:border-0">
            <div>
              <p class="font-medium text-gray-800">{{ item.name }}</p>
              <p class="text-sm text-gray-400">x{{ item.quantity }}</p>
            </div>
            <p class="font-bold text-gray-700">€{{ (item.price * item.quantity).toFixed(2) }}</p>
          </div>
          <div class="flex justify-between items-center mt-4 pt-4 border-t">
            <p class="text-lg font-bold">Total</p>
            <p class="text-2xl font-bold text-indigo-600">€{{ total.toFixed(2) }}</p>
          </div>
        </div>

        <!-- Checkout Form -->
        <div class="bg-white rounded-2xl shadow p-6">
          <h2 class="text-lg font-semibold mb-4">Your Details</h2>
          <div class="space-y-3 mb-6">
            <div class="grid grid-cols-2 gap-3">
              <input v-model="form.first_name" type="text" placeholder="First name" class="border rounded-xl px-4 py-2" />
              <input v-model="form.last_name" type="text" placeholder="Last name" class="border rounded-xl px-4 py-2" />
            </div>
            <input v-model="form.email" type="email" placeholder="Email" class="w-full border rounded-xl px-4 py-2" />
            <input v-model="form.phone" type="tel" placeholder="Phone" class="w-full border rounded-xl px-4 py-2" />
          </div>

          <h2 class="text-lg font-semibold mb-4">Payment</h2>
          <div id="payment-element" class="mb-4 p-3 border rounded-xl"></div>

          <div v-if="errorMessage" class="text-red-500 text-sm mb-4">{{ errorMessage }}</div>

          <button
            @click="submitPayment"
            :disabled="loading"
            class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 disabled:opacity-50"
          >
            <span v-if="loading">Processing...</span>
            <span v-else>Pay €{{ total.toFixed(2) }}</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  cartItems: Array,
  total: Number,
  stripeKey: String,
  clientSecret: String,
})

const form = ref({ first_name: '', last_name: '', email: '', phone: '' })
const loading = ref(false)
const errorMessage = ref('')
let stripe = null
let elements = null

const total = computed(() => props.total)

onMounted(() => {
  const script = document.createElement('script')
  script.src = 'https://js.stripe.com/v3/'
  script.onload = () => initStripe()
  document.head.appendChild(script)
})

function initStripe() {
  stripe = Stripe(props.stripeKey)
  elements = stripe.elements({ clientSecret: props.clientSecret })
  const paymentElement = elements.create('payment')
  paymentElement.mount('#payment-element')
}

async function submitPayment() {
  loading.value = true
  errorMessage.value = ''

  const { error, paymentIntent } = await stripe.confirmPayment({
    elements,
    redirect: 'if_required',
  })

  if (error) {
    errorMessage.value = error.message
    loading.value = false
    return
  }

  if (paymentIntent.status === 'succeeded') {
    router.post('/checkout', {
      ...form.value,
      payment_intent_id: paymentIntent.id,
    })
  }
}
</script>
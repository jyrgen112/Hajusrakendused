<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
});

const modules = [
    { title: 'Weather', emoji: '🌤️', description: 'Live weather data for any city using OpenWeatherMap API with caching.', href: '/weather', color: 'from-blue-400 to-cyan-500' },
    { title: 'Map & Markers', emoji: '🗺️', description: 'Interactive map to add, edit and delete location markers.', href: '/map', color: 'from-green-400 to-emerald-500' },
    { title: 'Blog', emoji: '📝', description: 'Write blog posts, leave comments and manage content with authentication.', href: '/blog', color: 'from-purple-400 to-violet-500' },
    { title: 'Shop', emoji: '🛍️', description: 'Browse products, manage your cart and checkout securely with Stripe.', href: '/shop', color: 'from-orange-400 to-pink-500' },
    { title: 'Warframe Alerts', emoji: '⚔️', description: 'Live Warframe game alerts, events and invasions from the official API.', href: '/warframe', color: 'from-gray-600 to-blue-800' },
]
</script>

<template>
    <Head title="Home" />
    <div class="min-h-screen bg-gray-950 text-white">
        <header class="border-b border-gray-800 px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-white">🚀 Hajusrakendused</h1>
            <nav class="flex gap-3">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm px-4 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 transition">Dashboard</Link>
                <template v-else>
                    <Link :href="route('login')" class="text-sm px-4 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 transition">Log in</Link>
                    <Link v-if="canRegister" :href="route('register')" class="text-sm px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 transition">Register</Link>
                </template>
            </nav>
        </header>
        <div class="text-center py-16 px-6">
            <h2 class="text-5xl font-extrabold mb-4 bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">Distributed Applications</h2>
            <p class="text-gray-400 text-lg max-w-xl mx-auto">A Laravel 11 project with 5 integrated feature modules built with Vue 3 + Inertia + Tailwind.</p>
        </div>
        <div class="max-w-6xl mx-auto px-6 pb-20">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a v-for="module in modules" :key="module.title" :href="module.href" class="group relative rounded-2xl overflow-hidden bg-gray-900 border border-gray-800 hover:border-gray-600 transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <div :class="`absolute inset-0 bg-gradient-to-br ${module.color} opacity-10 group-hover:opacity-20 transition`"></div>
                    <div class="relative p-6">
                        <div class="text-4xl mb-4">{{ module.emoji }}</div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ module.title }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ module.description }}</p>
                        <div class="mt-4 flex items-center text-indigo-400 text-sm font-semibold group-hover:text-indigo-300">
                            Open module
                            <svg class="ml-2 w-4 h-4 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <footer class="border-t border-gray-800 py-6 text-center text-sm text-gray-500">
            Laravel v{{ laravelVersion }} · PHP v{{ phpVersion }}
        </footer>
    </div>
</template>
<template>
  <div class="min-h-screen bg-white">
    <!-- Hero Section - Modern & Clean -->
    <section class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
      </div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="text-center">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white mb-6">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span class="text-sm font-medium">E-commerce Laravel + Vue 3</span>
          </div>

          <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white mb-6 tracking-tight">
            Shop Moderno e
            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 to-pink-200">
              Performante
            </span>
          </h1>
          
          <p class="text-xl sm:text-2xl text-indigo-100 mb-10 max-w-3xl mx-auto leading-relaxed">
            Piattaforma e-commerce completa con gestione prodotti avanzata, 
            carrello intelligente e dashboard amministrativa con analytics in tempo reale.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <router-link 
              to="/products" 
              class="group inline-flex items-center gap-2 bg-white text-indigo-600 px-8 py-4 rounded-xl hover:bg-indigo-50 transition-all duration-200 font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
              Esplora Prodotti
              <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </router-link>
            
            <router-link 
              v-if="!authStore.isAuthenticated"
              to="/register" 
              class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white px-8 py-4 rounded-xl border-2 border-white/30 hover:bg-white/20 transition-all duration-200 font-semibold text-lg">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
              </svg>
              Registrati Gratis
            </router-link>
            
            <router-link 
              v-else
              to="/dashboard" 
              class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white px-8 py-4 rounded-xl border-2 border-white/30 hover:bg-white/20 transition-all duration-200 font-semibold text-lg">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              Dashboard
            </router-link>
          </div>

          <!-- Stats -->
          <div class="mt-16 grid grid-cols-2 lg:grid-cols-4 gap-8 max-w-4xl mx-auto">
            <div class="text-center">
              <div class="text-4xl font-bold text-white mb-2">{{ stats.products }}+</div>
              <div class="text-indigo-200">Prodotti</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-bold text-white mb-2">{{ stats.users }}+</div>
              <div class="text-indigo-200">Utenti Attivi</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-bold text-white mb-2">{{ stats.orders }}+</div>
              <div class="text-indigo-200">Ordini Completati</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-bold text-white mb-2">99%</div>
              <div class="text-indigo-200">Soddisfazione</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Wave Divider -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
      </div>
    </section>

    <!-- Features Section - Modern Cards -->
    <section class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">
            Funzionalità Avanzate
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Tutto ciò di cui hai bisogno per gestire il tuo e-commerce in modo professionale
          </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-indigo-200">
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Gestione Utenti Completa</h3>
            <p class="text-gray-600 leading-relaxed">
              Sistema di autenticazione robusto con gestione ruoli (Admin, User, Business), 
              profili personalizzabili e controllo accessi granulare.
            </p>
          </div>

          <!-- Feature 2 -->
          <div class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-green-200">
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Carrello Intelligente</h3>
            <p class="text-gray-600 leading-relaxed">
              Gestione dinamica del carrello con aggiornamento quantità real-time, 
              calcolo totali automatico e controllo disponibilità prodotti.
            </p>
          </div>

          <!-- Feature 3 -->
          <div class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-purple-200">
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Dashboard con Analytics</h3>
            <p class="text-gray-600 leading-relaxed">
              Grafici interattivi con Chart.js, statistiche business in tempo reale 
              e analisi demografiche per decisioni data-driven.
            </p>
          </div>

          <!-- Feature 4 -->
          <div class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-yellow-200">
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Gestione Prodotti CRUD</h3>
            <p class="text-gray-600 leading-relaxed">
              Area admin completa per creare, modificare ed eliminare prodotti 
              con upload immagini e gestione scorte magazzino.
            </p>
          </div>

          <!-- Feature 5 -->
          <div class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-red-200">
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Sicurezza Avanzata</h3>
            <p class="text-gray-600 leading-relaxed">
              Laravel Sanctum per autenticazione API, validazione input lato server, 
              protezione CSRF e autorizzazioni basate su policies.
            </p>
          </div>

          <!-- Feature 6 -->
          <div class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-teal-200">
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Performance Ottimizzate</h3>
            <p class="text-gray-600 leading-relaxed">
              Vite per build ultra-veloce, lazy loading componenti Vue, 
              query ottimizzate e caching configurabile per massime prestazioni.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-gray-50" v-if="stats">
      <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
          📈 Statistiche Piattaforma
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ stats.users_count || 0 }}</div>
            <div class="text-gray-600">Utenti Registrati</div>
          </div>
          
          <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-green-600 mb-2">{{ stats.products_count || 0 }}</div>
            <div class="text-gray-600">Prodotti Disponibili</div>
          </div>
          
          <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-purple-600 mb-2">{{ stats.orders_count || 0 }}</div>
            <div class="text-gray-600">Ordini Completati</div>
          </div>
          
          <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-3xl font-bold text-orange-600 mb-2">€{{ formatCurrency(stats.total_revenue || 0) }}</div>
            <div class="text-gray-600">Fatturato Totale</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Technology Stack - Modern Design -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">
            Stack Tecnologico Moderno
          </h2>
          <p class="text-xl text-gray-600">
            Costruito con le migliori tecnologie per performance e scalabilità
          </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
          <!-- Laravel -->
          <div class="group bg-gradient-to-br from-red-50 to-orange-50 p-6 rounded-2xl hover:shadow-lg transition-all duration-300 cursor-pointer">
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 bg-red-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-10 h-10 text-white" viewBox="0 0 50 52" xmlns="http://www.w3.org/2000/svg">
                  <path fill="currentColor" d="M49.626 11.564a.809.809 0 0 1 .028.209v10.972a.8.8 0 0 1-.402.694l-9.209 5.302V39.25c0 .286-.152.55-.4.694L20.42 51.01c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 0 1-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054L.402 39.944A.801.801 0 0 1 0 39.25V6.334c0-.072.01-.142.028-.21.006-.023.02-.044.028-.067.015-.042.029-.085.051-.124.015-.026.037-.047.055-.071.023-.032.044-.065.071-.093.023-.023.053-.04.079-.06.029-.024.055-.05.088-.069h.001l9.61-5.533a.802.802 0 0 1 .8 0l9.61 5.533h.002c.032.02.059.045.088.068.026.02.055.038.078.06.028.029.048.062.072.094.017.024.04.045.054.071.023.04.036.082.052.124.008.023.022.044.028.068a.809.809 0 0 1 .028.209v20.559l8.008-4.611v-10.51c0-.07.01-.141.028-.208.007-.024.02-.045.028-.068.016-.042.03-.085.052-.124.015-.026.037-.047.054-.071.024-.032.044-.065.072-.093.023-.023.052-.04.078-.06.03-.024.056-.05.088-.069h.001l9.611-5.533a.801.801 0 0 1 .8 0l9.61 5.533c.034.02.06.045.09.068.025.02.054.038.077.06.028.029.048.062.072.094.018.024.04.045.054.071.023.039.036.082.052.124.009.023.022.044.028.068zm-1.574 10.718v-9.124l-3.363 1.936-4.646 2.675v9.124l8.01-4.611zm-9.61 16.505v-9.13l-4.57 2.61-13.05 7.448v9.216l17.62-10.144zM1.602 7.719v31.068L19.22 48.93v-9.214l-9.204-5.209-.003-.002-.004-.002c-.031-.018-.057-.044-.086-.066-.025-.02-.054-.036-.076-.058l-.002-.003c-.026-.025-.044-.056-.066-.084-.02-.027-.044-.05-.06-.078l-.001-.003c-.018-.03-.029-.066-.042-.1-.013-.03-.03-.058-.038-.09v-.001c-.01-.038-.012-.078-.016-.117-.004-.03-.012-.060-.012-.09v-.002-21.481L4.965 9.654 1.602 7.72zm8.81-5.994L2.405 6.334l8.005 4.609 8.006-4.61-8.006-4.608zm4.164 28.764l4.645-2.674V7.719l-3.363 1.936-4.646 2.675v20.096l3.364-1.937zM39.243 7.164l-8.006 4.609 8.006 4.609 8.005-4.61-8.005-4.608zm-.801 10.605l-4.646-2.675-3.363-1.936v9.124l4.645 2.674 3.364 1.937v-9.124zM20.02 38.33l11.743-6.704 5.87-3.35-8-4.606-9.211 5.303-8.395 4.833 7.993 4.524z"/>
                </svg>
              </div>
              <h4 class="font-bold text-gray-900 mb-1">Laravel 11</h4>
              <p class="text-sm text-gray-600 text-center">PHP Framework</p>
            </div>
          </div>

          <!-- Vue.js -->
          <div class="group bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl hover:shadow-lg transition-all duration-300 cursor-pointer">
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 bg-green-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-10 h-10 text-white" viewBox="0 0 256 221" xmlns="http://www.w3.org/2000/svg">
                  <path fill="currentColor" d="M204.8 0H256L128 220.8 0 0h97.92L128 51.2 157.44 0h47.36z"/>
                  <path fill="currentColor" fill-opacity="0.8" d="M0 0l128 220.8L256 0h-51.2L128 132.48 50.56 0H0z"/>
                </svg>
              </div>
              <h4 class="font-bold text-gray-900 mb-1">Vue 3</h4>
              <p class="text-sm text-gray-600 text-center">Progressive Framework</p>
            </div>
          </div>

          <!-- Tailwind CSS -->
          <div class="group bg-gradient-to-br from-cyan-50 to-blue-50 p-6 rounded-2xl hover:shadow-lg transition-all duration-300 cursor-pointer">
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 bg-cyan-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-10 h-10 text-white" viewBox="0 0 256 154" xmlns="http://www.w3.org/2000/svg">
                  <path fill="currentColor" d="M128 0C93.867 0 72.533 17.067 64 51.2 76.8 34.133 91.733 27.733 108.8 32c9.737 2.434 16.697 9.499 24.401 17.318C145.751 62.057 160.275 76.8 192 76.8c34.133 0 55.467-17.067 64-51.2-12.8 17.067-27.733 23.467-44.8 19.2-9.737-2.434-16.697-9.499-24.401-17.318C174.249 14.743 159.725 0 128 0zM64 76.8C29.867 76.8 8.533 93.867 0 128c12.8-17.067 27.733-23.467 44.8-19.2 9.737 2.434 16.697 9.499 24.401 17.318C81.751 138.857 96.275 153.6 128 153.6c34.133 0 55.467-17.067 64-51.2-12.8 17.067-27.733 23.467-44.8 19.2-9.737-2.434-16.697-9.499-24.401-17.318C110.249 91.543 95.725 76.8 64 76.8z"/>
                </svg>
              </div>
              <h4 class="font-bold text-gray-900 mb-1">Tailwind CSS</h4>
              <p class="text-sm text-gray-600 text-center">Utility-First CSS</p>
            </div>
          </div>

          <!-- MySQL -->
          <div class="group bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl hover:shadow-lg transition-all duration-300 cursor-pointer">
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
              </div>
              <h4 class="font-bold text-gray-900 mb-1">MySQL</h4>
              <p class="text-sm text-gray-600 text-center">Database Relazionale</p>
            </div>
          </div>
        </div>

        <!-- Additional technologies -->
        <div class="mt-12 grid grid-cols-3 md:grid-cols-6 gap-4">
          <div class="text-center">
            <div class="text-3xl mb-2">⚡</div>
            <p class="text-sm font-medium text-gray-700">Vite</p>
          </div>
          <div class="text-center">
            <div class="text-3xl mb-2">📊</div>
            <p class="text-sm font-medium text-gray-700">Chart.js</p>
          </div>
          <div class="text-center">
            <div class="text-3xl mb-2">🔐</div>
            <p class="text-sm font-medium text-gray-700">Sanctum</p>
          </div>
          <div class="text-center">
            <div class="text-3xl mb-2">🎯</div>
            <p class="text-sm font-medium text-gray-700">Pinia</p>
          </div>
          <div class="text-center">
            <div class="text-3xl mb-2">🌐</div>
            <p class="text-sm font-medium text-gray-700">Axios</p>
          </div>
          <div class="text-center">
            <div class="text-3xl mb-2">🧪</div>
            <p class="text-sm font-medium text-gray-700">PHPUnit</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Final - Modern & Engaging -->
    <section class="relative py-24 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 overflow-hidden">
      <!-- Animated background -->
      <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMSI+PHBhdGggZD0iTTM2IDM0di00aC0ydjRoLTR2Mmg0djRoMnYtNGg0di0yaC00em0wLTMwVjBoLTJ2NGgtNHYyaDR2NGgyVjZoNFY0aC00ek02IDM0di00SDR2NEgwdjJoNHY0aDJ2LTRoNHYtMkg2ek02IDRWMEY0djRIMHYyaDR2NGgyVjZoNFY0SDZ6Ii8+PC9nPjwvZz48L3N2Zz4=')]"></div>
      </div>

      <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl sm:text-5xl font-extrabold text-white mb-6">
          Inizia la Tua Esperienza Oggi
        </h2>
        <p class="text-xl text-indigo-100 mb-10 max-w-2xl mx-auto">
          Unisciti a centinaia di utenti che già utilizzano la nostra piattaforma per 
          gestire i loro acquisti in modo semplice e sicuro.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
          <router-link 
            v-if="!authStore.isAuthenticated"
            to="/register" 
            class="group inline-flex items-center gap-2 bg-white text-indigo-600 px-10 py-5 rounded-2xl hover:bg-indigo-50 transition-all duration-200 font-bold text-lg shadow-2xl hover:shadow-3xl transform hover:-translate-y-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Registrati Gratis
            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </router-link>
          
          <router-link 
            to="/products" 
            class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border-2 border-white text-white px-10 py-5 rounded-2xl hover:bg-white/20 transition-all duration-200 font-bold text-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Esplora Prodotti
          </router-link>
        </div>

        <!-- Trust indicators -->
        <div class="mt-16 flex flex-wrap justify-center items-center gap-8 text-white/80">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm">100% Sicuro</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm">Supporto 24/7</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm">Garanzia Soddisfatti</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const stats = ref({
  products: 50,
  users: 150,
  orders: 200
})
</script>
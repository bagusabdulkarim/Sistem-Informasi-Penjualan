<template>
  <div class="font-sans text-slate-900">
    <div v-if="!isLoggedIn">
      <Login v-if="authPage === 'login'" @login-success="checkToken" @switch-view="authPage = 'register'" />
      <Register v-if="authPage === 'register'" @register-success="authPage = 'login'" @switch-view="authPage = 'login'" />
    </div>

    <div v-else class="min-h-screen bg-slate-50 flex overflow-hidden">

      <aside
        class="fixed inset-y-0 left-0 z-50 w-64 bg-indigo-950 text-slate-300 transform transition-transform duration-300 md:relative md:translate-x-0 shadow-2xl"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
      >
        <div class="p-6 text-white flex items-center justify-between border-b border-indigo-900/50">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center font-bold">S</div>
            <span class="text-xl font-bold tracking-tight text-white">SIP Admin</span>
          </div>
          <button @click="sidebarOpen = false" class="md:hidden text-indigo-300">✕</button>
        </div>

        <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-80px)]">
          <p class="px-4 text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-2">Menu Utama</p>

          <button @click="navigate('dashboard')" :class="menuClass('dashboard')">
            <span class="mr-3">📊</span> Dashboard
          </button>

          <button @click="navigate('pelanggan')" :class="menuClass('pelanggan')">
            <span class="mr-3">👥</span> Data Pelanggan
          </button>

          <button @click="navigate('produk')" :class="menuClass('produk')">
            <span class="mr-3">📦</span> Data Produk
          </button>

          <p class="px-4 text-[10px] font-bold text-indigo-400 uppercase tracking-widest mt-6 mb-2">Transaksi</p>

          <button @click="navigate('pesanan')" :class="menuClass('pesanan')">
            <span class="mr-3">🛒</span> Pesanan
          </button>

          <button @click="navigate('faktur')" :class="menuClass('faktur')">
            <span class="mr-3">📜</span> Data Faktur
          </button>

          <button @click="navigate('kuitansi')" :class="menuClass('kuitansi')">
            <span class="mr-3">💳</span> Data Kuitansi
          </button>

          <button @click="navigate('laporan')" :class="menuClass('laporan')">
            <span class="mr-3">📊</span> Laporan
          </button>

          <div class="pt-6 mt-6 border-t border-indigo-900">
            <button @click="handleLogout" class="w-full flex items-center px-4 py-2 text-red-400 hover:bg-red-500/10 rounded-xl transition">
              <span class="mr-3">🚪</span> Logout
            </button>
          </div>
        </nav>
      </aside>

      <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/60 z-40 md:hidden backdrop-blur-sm"></div>

      <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 md:px-8 shadow-sm">
          <button @click="sidebarOpen = true" class="md:hidden p-2 text-slate-600">
            <span class="text-2xl">☰</span>
          </button>

          <div class="flex items-center gap-4">
            <div class="hidden md:block text-sm text-slate-500 font-medium">
              {{ currentDate }}
            </div>
          </div>

          <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
              <p class="text-xs font-bold text-slate-800 uppercase">Administrator</p>
              <p class="text-[10px] text-indigo-500">Online</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white shadow-sm overflow-hidden text-center flex items-center justify-center font-bold text-slate-500">
              A
            </div>
          </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
          <div v-if="view === 'dashboard'" class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-in fade-in duration-500">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
              <p class="text-slate-500 text-sm">Selamat Datang</p>
              <h2 class="text-xl font-bold">Halo, Admin! 👋</h2>
            </div>
            </div>

          <div v-if="view === 'pelanggan'">
            <PelangganIndex />
          </div>

          <div v-if="view === 'produk'">
            <ProdukIndex />
          </div>

          <div v-if="view === 'pesanan'">
            <PesanIndex />
          </div>

          <div v-if="view === 'faktur'">
            <FakturIndex />
          </div>

          <div v-if="view === 'kuitansi'">
            <KuitansiIndex />
          </div>
          <div v-if="view === 'laporan'">
            <LaporanIndex />
          </div>

          <div v-if="[ ].includes(view)" class="bg-white p-10 rounded-2xl text-center shadow-sm">
             <div class="text-5xl mb-4">🚧</div>
             <h2 class="text-xl font-bold uppercase">Halaman {{ view }}</h2>
             <p class="text-slate-500">Sedang dalam pengembangan...</p>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import Login from './auth/Login.vue'
import Register from './auth/Register.vue'
import PelangganIndex from './pelanggan/Index.vue'
import ProdukIndex from './produk/Index.vue'
import PesanIndex from './pesan/Index.vue'
import FakturIndex from './faktur/Index.vue'
import KuitansiIndex from './kuitansi/Index.vue'
import LaporanIndex from './laporan/Index.vue'

const isLoggedIn = ref(false)
const authPage = ref('login')
const view = ref('dashboard')
const sidebarOpen = ref(false)


const currentDate = computed(() => {
    return new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
})

const navigate = (page) => {
  view.value = page
  sidebarOpen.value = false // Tutup sidebar di mobile setelah klik
}

const menuClass = (target) => {
  const base = "w-full flex items-center px-4 py-3 rounded-xl transition-all duration-200 text-sm font-medium "
  return view.value === target
    ? base + "bg-indigo-600 text-white shadow-lg shadow-indigo-900/50 translate-x-1"
    : base + "text-indigo-200 hover:bg-white/10 hover:text-white"
}



const checkToken = () => {
  isLoggedIn.value = !!localStorage.getItem('auth_token')
}

const handleLogout = () => {
  if (confirm('Keluar dari sistem?')) {
    localStorage.removeItem('auth_token')
    isLoggedIn.value = false
    authPage.value = 'login'
  }
}

onMounted(() => checkToken())
</script>

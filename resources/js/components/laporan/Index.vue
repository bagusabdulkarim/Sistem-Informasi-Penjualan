<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Dashboard & Laporan</h1>
        <p class="text-slate-500 text-sm">Analisis performa penjualan dan unduh laporan PDF.</p>
      </div>
      <button
        @click="exportPDF"
        class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg flex items-center gap-2"
      >
        <span>📄</span> Laporan Seluruh Transaksi
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-indigo-600 p-6 rounded-3xl text-white shadow-xl">
        <p class="text-indigo-100 text-sm">Total Omzet</p>
        <h2 class="text-3xl font-black mt-1">Rp {{ stats.grand_total?.toLocaleString() }}</h2>
      </div>
      <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <p class="text-slate-500 text-sm">Jumlah Transaksi</p>
        <h2 class="text-3xl font-black text-slate-800 mt-1">{{ stats.data?.length }}</h2>
      </div>
      <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <p class="text-slate-500 text-sm">Produk Terlaris</p>
        <h2 class="text-xl font-bold text-indigo-600 mt-1">{{ terlaris[0]?.nama_produk || '-' }}</h2>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-800 mb-4">Riwayat Penjualan Terbaru</h3>
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-slate-400 border-b">
              <th class="pb-3">Nota</th>
              <th class="pb-3">Pelanggan</th>
              <th class="pb-3 text-right">Total</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="item in stats.data" :key="item.nomor_nota">
              <td class="py-3 font-mono text-indigo-600">#{{ item.nomor_nota }}</td>
              <td class="py-3 font-medium">{{ item.pelanggan }}</td>
              <td class="py-3 text-right font-bold">Rp {{ item.total_bayar?.toLocaleString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-800 mb-4">Produk Paling Laku</h3>
        <div class="space-y-4">
          <div v-for="(p, index) in terlaris" :key="p.id_produk" class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full font-bold text-xs">{{ index + 1 }}</span>
              <div>
                <p class="font-bold text-slate-800">{{ p.nama_produk }}</p>
                <p class="text-xs text-slate-500">{{ p.total_terjual }} {{ p.satuan }} terjual</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref({})
const terlaris = ref([])

const loadData = async () => {
  try {
    const [resPenjualan, resTerlaris] = await Promise.all([
      axios.get('/api/laporan/penjualan'),
      axios.get('/api/laporan/produk-terlaris')
    ])
    stats.value = resPenjualan.data
    terlaris.value = resTerlaris.data.data
  } catch (err) {
    console.error("Gagal memuat data laporan")
  }
}

const exportPDF = () => {
  // Buka URL export PDF di tab baru
  window.open('http://localhost:8000/api/laporan/export-pdf', '_blank')
}

onMounted(() => loadData())
</script>

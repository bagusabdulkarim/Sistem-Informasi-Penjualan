<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Manajemen Faktur</h1>
        <p class="text-slate-500 text-sm">Cetak dan kelola faktur penjualan pelanggan.</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-1 space-y-4">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
          <h3 class="font-bold text-slate-700 mb-4">Pilih Pesanan</h3>
          <div class="space-y-2">
            <div
              v-for="nota in listPesan"
              :key="nota.id_pesan"
              @click="selectPesanan(nota)"
              :class="selectedNota?.id_pesan === nota.id_pesan ? 'border-indigo-500 bg-indigo-50' : 'border-slate-100 hover:bg-slate-50'"
              class="p-4 border-2 rounded-xl cursor-pointer transition-all"
            >
              <div class="flex justify-between items-center">
                <span class="font-mono font-bold text-indigo-600">#{{ nota.id_pesan }}</span>
                <span class="text-[10px] text-slate-400">{{ nota.tgl_pesan }}</span>
              </div>
              <p class="text-sm font-medium text-slate-700 mt-1">{{ nota.pelanggan?.nm_pelanggan || 'Umum' }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-2">
        <div v-if="selectedNota" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden min-h-[500px] flex flex-col">
          <div id="printArea" class="p-8 flex-1">
            <div class="flex justify-between items-start border-b-2 border-slate-800 pb-6 mb-6">
              <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tighter">FAKTUR PENJUALAN</h2>
                <p class="text-slate-500 font-mono">ID PESAN: {{ selectedNota.id_pesan }}</p>
              </div>
              <div class="text-right">
                <h4 class="font-bold text-slate-800">Nama Toko Anda</h4>
                <p class="text-xs text-slate-500">Jl. Raya No. 123, Bandung</p>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-8 mb-8 text-sm">
              <div>
                <p class="text-slate-400 uppercase font-bold text-[10px]">Ditagihkan Kepada:</p>
                <p class="font-bold text-slate-800 text-lg">{{ selectedNota.pelanggan?.nm_pelanggan || 'Pelanggan Umum' }}</p>
                <p class="text-slate-500">{{ selectedNota.id_pelanggan }}</p>
              </div>
              <div class="text-right">
                <p class="text-slate-400 uppercase font-bold text-[10px]">Tanggal:</p>
                <p class="font-bold text-slate-800">{{ selectedNota.tgl_pesan }}</p>
              </div>
            </div>

            <table class="w-full text-sm mb-8">
              <thead>
                <tr class="border-b-2 border-slate-100">
                  <th class="py-3 text-left">Deskripsi Produk</th>
                  <th class="py-3 text-right">Harga</th>
                  <th class="py-3 text-center">Qty</th>
                  <th class="py-3 text-right">Total</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50">
                <tr v-for="item in selectedNota.detil_pesan" :key="item.id_produk">
                  <td class="py-4">
                    <span class="font-bold text-slate-700">{{ item.produk?.nm_produk }}</span>
                  </td>
                  <td class="py-4 text-right">Rp {{ parseInt(item.harga).toLocaleString() }}</td>
                  <td class="py-4 text-center">{{ item.jumlah }}</td>
                  <td class="py-4 text-right font-bold text-slate-800">Rp {{ (item.harga * item.jumlah).toLocaleString() }}</td>
                </tr>
              </tbody>
            </table>

            <div class="border-t-2 border-slate-800 pt-4 flex justify-end">
              <div class="w-64 space-y-2">
                <div class="flex justify-between font-bold text-xl">
                  <span>Grand Total</span>
                  <span class="text-indigo-600">Rp {{ calculateTotal(selectedNota.detil_pesan).toLocaleString() }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="p-6 bg-slate-50 border-t flex justify-end gap-3">
            <button @click="printFaktur" class="px-6 py-2 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition flex items-center gap-2">
              <span>🖨️</span> Cetak Faktur
            </button>
          </div>
        </div>

        <div v-else class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl h-full flex items-center justify-center text-slate-400">
          Silakan pilih nomor pesanan di samping untuk melihat faktur
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const listPesan = ref([])
const selectedNota = ref(null)

const loadPesanan = async () => {
  try {
    const res = await axios.get('/api/pesan')
    listPesan.value = res.data.data
  } catch (err) {
    console.error("Gagal memuat data pesanan")
  }
}

const selectPesanan = async (nota) => {
  // Kita fetch ulang detail lengkapnya menggunakan ID
  try {
    const res = await axios.get(`/api/pesan/${nota.id_pesan}`)
    selectedNota.value = res.data.data
  } catch (err) {
    alert("Gagal mengambil rincian pesanan")
  }
}

const calculateTotal = (items) => {
  if (!items) return 0
  return items.reduce((acc, item) => acc + (item.harga * item.jumlah), 0)
}

const printFaktur = () => {
  // Fungsi cetak sederhana menggunakan window.print
  const printContents = document.getElementById('printArea').innerHTML
  const originalContents = document.body.innerHTML

  document.body.innerHTML = printContents
  window.print()
  document.body.innerHTML = originalContents
  window.location.reload() // Reload untuk mengembalikan state Vue
}

onMounted(() => {
  loadPesanan()
})
</script>

<style scoped>
@media print {
  body * {
    visibility: hidden;
  }
  #printArea, #printArea * {
    visibility: visible;
  }
  #printArea {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
}
</style>

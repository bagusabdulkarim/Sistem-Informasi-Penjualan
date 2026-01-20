<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Bukti Pembayaran (Kuitansi)</h1>
        <p class="text-slate-500 text-sm">Input pembayaran dan cetak bukti bayar pelanggan.</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-1 space-y-4">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
          <h3 class="font-bold text-slate-700 mb-4">Pilih Transaksi</h3>
          <div class="space-y-2 max-h-[500px] overflow-y-auto pr-2">
            <div
              v-for="nota in listPesan"
              :key="nota.id_pesan"
              @click="selectNota(nota)"
              :class="selectedNota?.id_pesan === nota.id_pesan ? 'border-emerald-500 bg-emerald-50' : 'border-slate-100 hover:bg-slate-50'"
              class="p-4 border-2 rounded-xl cursor-pointer transition-all"
            >
              <div class="flex justify-between">
                <span class="font-bold text-slate-800">#{{ nota.id_pesan }}</span>
                <span class="text-xs text-slate-400">{{ nota.tgl_pesan }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-2 space-y-6">
        <div v-if="selectedNota" class="space-y-6">
          <div id="printKuitansi" class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xl p-10 border-[10px] border-double border-slate-100">
            <div class="flex justify-between items-start mb-10 border-b pb-4">
              <div>
                <h2 class="text-4xl font-serif italic font-black text-slate-800">Kuitansi</h2>
                <p class="text-slate-400 font-mono text-sm">No. {{ selectedNota.id_pesan }}/KUI/{{ new Date().getFullYear() }}</p>
              </div>
              <div class="text-right">
                <p class="font-bold text-emerald-600 uppercase text-sm">Status: LUNAS</p>
              </div>
            </div>

            <div class="space-y-6 text-slate-700">
              <div class="flex border-b border-dotted border-slate-300 pb-2">
                <span class="w-48 italic">Telah terima dari :</span>
                <span class="font-bold uppercase text-lg">{{ selectedNota.pelanggan?.nm_pelanggan || '................................' }}</span>
              </div>

              <div class="flex border-b border-dotted border-slate-300 pb-2">
                <span class="w-48 italic">Uang Sejumlah :</span>
                <span class="font-bold italic bg-slate-50 px-4 py-1 rounded">
                  ## {{ terbilang(totalTagihan) }} Rupiah ##
                </span>
              </div>

              <div class="flex border-b border-dotted border-slate-300 pb-2">
                <span class="w-48 italic">Untuk Pembayaran :</span>
                <span>Pelunasan transaksi pada tanggal {{ selectedNota.tgl_pesan }}</span>
              </div>
            </div>

            <div class="mt-12 flex justify-between items-end">
              <div class="space-y-2">
                <div class="flex justify-between w-64 text-sm border-b border-slate-100 pb-1">
                  <span>Total Tagihan:</span>
                  <span>Rp {{ totalTagihan?.toLocaleString() }}</span>
                </div>
                <div class="flex justify-between w-64 text-sm border-b border-slate-100 pb-1">
                  <span>Dibayarkan:</span>
                  <span>Rp {{ nominalBayar?.toLocaleString() }}</span>
                </div>
                <div class="flex justify-between w-64 text-lg font-black text-emerald-700">
                  <span class="italic font-normal text-sm text-slate-400">Kembalian:</span>
                  <span>Rp {{ kembalian?.toLocaleString() }}</span>
                </div>

                <div class="mt-6 bg-emerald-600 text-white px-8 py-3 rounded-sm text-2xl font-black italic shadow-inner inline-block">
                  Rp {{ totalTagihan?.toLocaleString() }},-
                </div>
              </div>

              <div class="text-center w-48">
                <p class="text-xs mb-16">Bandung, {{ new Date().toLocaleDateString('id-ID') }}</p>
                <p class="font-bold border-t border-slate-800 pt-1 text-sm">KASIR TOKO</p>
              </div>
            </div>
          </div>

          <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center gap-6">
            <div class="flex-1 w-full">
              <label class="block text-sm font-bold text-slate-700 mb-2">Uang Fisik Diterima (Rp)</label>
              <input
                v-model.number="nominalBayar"
                type="number"
                class="w-full p-4 bg-slate-50 border-2 border-slate-200 rounded-xl text-2xl font-black text-emerald-600 focus:border-emerald-500 focus:ring-0 transition-all"
                placeholder="Masukkan nominal uang..."
              >
            </div>
            <div class="pt-6">
              <button
                @click="printKuitansi"
                :disabled="nominalBayar < totalTagihan"
                class="bg-emerald-600 text-white px-10 py-4 rounded-xl font-bold hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed flex items-center gap-2 shadow-lg shadow-emerald-200 transition-all"
              >
                <span>🖨️</span> Cetak Kuitansi
              </button>
            </div>
          </div>
        </div>

        <div v-else class="bg-slate-50 border-2 border-dashed rounded-3xl h-full min-h-[400px] flex items-center justify-center text-slate-400">
          Pilih nomor transaksi di samping untuk memproses pembayaran
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const listPesan = ref([])
const selectedNota = ref(null)
const nominalBayar = ref(0) // State baru untuk uang fisik

const totalTagihan = computed(() => {
  if (!selectedNota.value || !selectedNota.value.detil_pesan) return 0
  return selectedNota.value.detil_pesan.reduce((acc, item) => acc + (item.harga * item.jumlah), 0)
})

// Logika hitung kembalian otomatis
const kembalian = computed(() => {
  if (!selectedNota.value) return 0
  const hasil = nominalBayar.value - totalTagihan.value
  return hasil > 0 ? hasil : 0
})

const loadPesan = async () => {
  const res = await axios.get('/api/pesan')
  listPesan.value = res.data.data
}

const selectNota = async (nota) => {
  const res = await axios.get(`/api/pesan/${nota.id_pesan}`)
  selectedNota.value = res.data.data
  nominalBayar.value = selectedNota.value.total_bayar // Set default sama dengan total
}

const terbilang = (n) => {
  if (!n) return ""
  const angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"]
  let hasil = ""
  if (n < 12) hasil = angka[n]
  else if (n < 20) hasil = terbilang(n - 10) + " Belas"
  else if (n < 100) hasil = terbilang(Math.floor(n / 10)) + " Puluh " + terbilang(n % 10)
  else if (n < 200) hasil = " Seratus " + terbilang(n - 100)
  else if (n < 1000) hasil = terbilang(Math.floor(n / 100)) + " Ratus " + terbilang(n % 100)
  else if (n < 2000) hasil = " Seribu " + terbilang(n - 1000)
  else if (n < 1000000) hasil = terbilang(Math.floor(n / 1000)) + " Ribu " + terbilang(n % 1000)
  else if (n < 1000000000) hasil = terbilang(Math.floor(n / 1000000)) + " Juta " + terbilang(n % 1000000)
  return hasil.replace(/\s+/g, ' ').trim()
}

const printKuitansi = () => {
  const printContents = document.getElementById('printKuitansi').innerHTML
  const originalContents = document.body.innerHTML
  document.body.innerHTML = printContents
  window.print()
  document.body.innerHTML = originalContents
  window.location.reload()
}

onMounted(() => loadPesan())
</script>

<style scoped>
@media print {
  body * { visibility: hidden; }
  #printKuitansi, #printKuitansi * { visibility: visible; }
  #printKuitansi { position: absolute; left: 0; top: 0; width: 100%; border: none; padding: 0; margin: 0; }
}
</style>

<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">{{ isCreating ? 'Kasir / Transaksi Baru' : 'Riwayat Pesanan' }}</h1>
        <p class="text-slate-500 text-sm">Kelola transaksi penjualan barang.</p>
      </div>
      <button
        @click="toggleMode"
        :class="isCreating ? 'bg-slate-100 text-slate-600' : 'bg-indigo-600 text-white'"
        class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2"
      >
        {{ isCreating ? '⬅️ Kembali' : '🛒 Buat Pesanan' }}
      </button>
    </div>

    <div v-if="isCreating" class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-in slide-in-from-bottom-4">
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
          <h3 class="font-bold text-slate-700 mb-4">Pilih Produk</h3>
          <div class="flex flex-wrap gap-4">
            <select v-model="selectedProduct" class="flex-1 p-3 border rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
              <option :value="null">-- Pilih Barang --</option>
              <option v-for="p in listProduk" :key="p.id_produk" :value="p">
                {{ p.nm_produk }} (Stok: {{ p.stock }}) - Rp {{ p.harga.toLocaleString() }}
              </option>
            </select>
            <input v-model.number="qtyInput" type="number" class="w-24 p-3 border rounded-xl" placeholder="Qty">
            <button @click="addToCart" class="px-6 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700">Tambah</button>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <table class="w-full text-left">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Barang</th>
                <th class="px-6 py-4 text-right text-xs font-bold uppercase text-slate-500">Harga</th>
                <th class="px-6 py-4 text-center text-xs font-bold uppercase text-slate-500">Qty</th>
                <th class="px-6 py-4 text-right text-xs font-bold uppercase text-slate-500">Subtotal</th>
                <th class="px-6 py-4"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="(item, idx) in cart" :key="idx">
                <td class="px-6 py-4 font-medium">{{ item.nm_produk }}</td>
                <td class="px-6 py-4 text-right">Rp {{ item.harga.toLocaleString() }}</td>
                <td class="px-6 py-4 text-center">{{ item.jumlah }}</td>
                <td class="px-6 py-4 text-right font-bold text-indigo-600">Rp {{ (item.harga * item.jumlah).toLocaleString() }}</td>
                <td class="px-6 py-4 text-center">
                  <button @click="cart.splice(idx, 1)" class="text-red-400 hover:text-red-600">🗑️</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm h-fit space-y-4">
        <h3 class="font-bold text-slate-700">Detail Transaksi</h3>
        <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase">No. Nota (Auto-Increment)</label>
        <input
            v-model.number="formHeader.id_pesan"
            type="number"
            readonly
            class="w-full p-2 bg-slate-100 border-none font-mono font-bold text-indigo-600 rounded-lg"
        >
        </div>
        <div>
          <label class="text-[10px] font-bold text-slate-400 uppercase">Pelanggan</label>
          <select v-model="formHeader.id_pelanggan" class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">-- Pilih Pelanggan --</option>
            <option v-for="plg in listPelanggan" :key="plg.id_pelanggan" :value="plg.id_pelanggan">
              {{ plg.nm_pelanggan }}
            </option>
          </select>
        </div>
        <div class="pt-4 border-t border-dashed">
          <div class="flex justify-between items-center mb-6">
            <span class="text-slate-500 font-medium">Total Bayar</span>
            <span class="text-2xl font-black text-slate-800">Rp {{ grandTotal.toLocaleString() }}</span>
          </div>
          <button @click="submitTransaction" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">SIMPAN TRANSAKSI</button>
        </div>
      </div>
    </div>

    <div v-else class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden animate-in fade-in">
      <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 border-b">
          <tr>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">No. Nota</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Tanggal</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Pelanggan</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="nota in listPesan" :key="nota.id_pesan" class="hover:bg-slate-50 transition">
            <td class="px-6 py-4 font-mono font-bold text-indigo-600">{{ nota.id_pesan }}</td>
            <td class="px-6 py-4 text-slate-600">{{ nota.tgl_pesan }}</td>
            <td class="px-6 py-4 font-bold text-slate-700">
              {{ nota.pelanggan ? nota.pelanggan.nm_pelanggan : 'Tanpa Pelanggan' }}
            </td>
            <td class="px-6 py-4 text-center">
                <button @click="viewDetail(nota.id_pesan)" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">
                    Lihat Detail
                </button>
              <button @click="deletePesan(nota.id_pesan)" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModalDetail" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-indigo-600">
        <h3 class="text-white font-bold text-lg">Detail Nota #{{ detailNota?.id_pesan }}</h3>
        <button @click="showModalDetail = false" class="text-white/80 hover:text-white text-2xl">&times;</button>
        </div>

        <div class="p-6 space-y-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
            <p class="text-slate-400 font-bold uppercase text-[10px]">Pelanggan</p>
            <p class="font-bold text-slate-700">{{ detailNota?.pelanggan?.nm_pelanggan || 'Umum' }}</p>
            <p class="text-slate-500">{{ detailNota?.id_pelanggan }}</p>
            </div>
            <div class="text-right">
            <p class="text-slate-400 font-bold uppercase text-[10px]">Tanggal Transaksi</p>
            <p class="font-bold text-slate-700">{{ detailNota?.tgl_pesan }}</p>
            </div>
        </div>

        <div class="border rounded-2xl overflow-hidden">
            <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b">
                <tr>
                <th class="px-4 py-3 font-bold text-slate-600">Produk</th>
                <th class="px-4 py-3 text-right font-bold text-slate-600">Harga</th>
                <th class="px-4 py-3 text-center font-bold text-slate-600">Qty</th>
                <th class="px-4 py-3 text-right font-bold text-slate-600">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in detailNota?.detil_pesan" :key="item.id_produk" class="border-b last:border-0">
                <td class="px-4 py-3">
                    <span class="font-medium text-slate-700">{{ item.produk?.nm_produk || 'Produk dihapus' }}</span>
                    <p class="text-[10px] text-slate-400">{{ item.id_produk }}</p>
                </td>
                <td class="px-4 py-3 text-right">Rp {{ parseInt(item.harga).toLocaleString() }}</td>
                <td class="px-4 py-3 text-center">{{ item.jumlah }}</td>
                <td class="px-4 py-3 text-right font-bold text-indigo-600">
                    Rp {{ (item.harga * item.jumlah).toLocaleString() }}
                </td>
                </tr>
            </tbody>
            <tfoot class="bg-slate-50 font-black">
                <tr>
                <td colspan="3" class="px-4 py-4 text-right text-slate-500">TOTAL PEMBAYARAN</td>
                <td class="px-4 py-4 text-right text-indigo-600 text-lg">
                    Rp {{ detailNota?.total_bayar?.toLocaleString() || calculateTotal(detailNota?.detil_pesan).toLocaleString() }}
                </td>
                </tr>
            </tfoot>
            </table>
        </div>
        </div>

    <div class="p-6 bg-slate-50 border-t flex justify-end">
      <button @click="showModalDetail = false" class="px-6 py-2 bg-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-300 transition">Tutup</button>
    </div>
  </div>
</div>

  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue'
import axios from 'axios'

// --- STATE ---
const listPesan = ref([])
const listProduk = ref([])
const listPelanggan = ref([])
const isCreating = ref(false)
const cart = ref([])
const showModalDetail = ref(false)
const detailNota = ref(null)

// Form Header
const formHeader = reactive({
  id_pesan: '',
  tgl_pesan: new Date().toISOString().split('T')[0],
  id_pelanggan: ''
})

// Input pilih barang
const selectedProduct = ref(null)
const qtyInput = ref(1)

// --- FUNGSI ---

// 1. Generate ID Nota Otomatis
const generateID = () => {
  // Jika listPesan kosong, mulai dari 10001
  if (listPesan.value.length === 0) {
    formHeader.id_pesan = 10001;
  } else {
    // Ambil ID terbesar dari list yang ada, lalu tambah 1
    const maxID = Math.max(...listPesan.value.map(p => parseInt(p.id_pesan)));

    // Validasi agar tidak lebih dari 5 digit (99999)
    if (maxID >= 99999) {
      alert("Peringatan: ID sudah mencapai batas maksimal Integer(5)");
      formHeader.id_pesan = 99999;
    } else {
      formHeader.id_pesan = maxID + 1;
    }
  }
}

// 2. Toggle Mode Ganti Riwayat/Kasir
const toggleMode = () => {
  if (!isCreating.value) {
    generateID()
    cart.value = []
    formHeader.id_pelanggan = ''
  }
  isCreating.value = !isCreating.value
}

// 3. Tambah Barang ke Keranjang (Klien)
const addToCart = () => {
  if (!selectedProduct.value) return alert("Pilih produk dulu!")
  if (qtyInput.value < 1) return alert("Jumlah minimal 1")

  // Validasi stok di sisi klien
  if (qtyInput.value > selectedProduct.value.stock) {
    return alert(`Stok tidak cukup! Sisa: ${selectedProduct.value.stock}`)
  }

  const existing = cart.value.find(i => i.id_produk === selectedProduct.value.id_produk)
  if (existing) {
    existing.jumlah += qtyInput.value
  } else {
    cart.value.push({
      id_produk: selectedProduct.value.id_produk,
      nm_produk: selectedProduct.value.nm_produk,
      harga: selectedProduct.value.harga,
      jumlah: qtyInput.value
    })
  }
  selectedProduct.value = null
  qtyInput.value = 1
}

const grandTotal = computed(() => {
  return cart.value.reduce((acc, item) => acc + (item.harga * item.jumlah), 0)
})

// 4. Kirim ke Server (PesanController@store)
const submitTransaction = async () => {
  if (!formHeader.id_pelanggan) return alert("Pilih pelanggan terlebih dahulu!")
  if (cart.value.length === 0) return alert("Keranjang masih kosong!")

  try {
    const payload = {
      id_pesan: formHeader.id_pesan,
      tgl_pesan: formHeader.tgl_pesan,
      id_pelanggan: formHeader.id_pelanggan,
      items: cart.value // Array berisi id_produk, jumlah, dan harga
    }

    // Melakukan request POST ke server
    const res = await axios.post('/api/pesan', payload)

    // Cek apakah response sukses (Status 200 atau 201)
    if (res.data.success || res.status === 201 || res.status === 200) {
      alert("Data transaksi berhasil disimpan!") // Pesan sesuai permintaanmu

      // RESET FORM & KEMBALI KE RIWAYAT
      isCreating.value = false
      cart.value = []
      formHeader.id_pelanggan = ''

      // Refresh data tabel riwayat agar transaksi terbaru muncul
      loadInitialData()
    }
  } catch (err) {
    console.error(err)
    // Jika ada error validasi dari Laravel (seperti stok habis di tengah jalan)
    const errorMsg = err.response?.data?.message || "Terjadi kesalahan saat menyimpan transaksi."
    alert("Gagal: " + errorMsg)
  }
}

// 5. Hapus Pesanan (PesanController@destroy)
const deletePesan = async (id) => {
  if (confirm(`Hapus nota ${id}? Stok akan dikembalikan.`)) {
    try {
      await axios.delete(`/api/pesan/${id}`)
      loadInitialData()
    } catch (err) { alert("Gagal hapus") }
  }
}

// 6. Load Data Awal
const loadInitialData = async () => {
  const [resPesan, resProd, resPel] = await Promise.all([
    axios.get('/api/pesan'),
    axios.get('/api/produk'),
    axios.get('/api/pelanggan')
  ])
  listPesan.value = resPesan.data.data
  listProduk.value = resProd.data.data
  listPelanggan.value = resPel.data.data
}
// 7. View Detail Nota
const viewDetail = async (id) => {
  try {
    const res = await axios.get(`/api/pesan/${id}`)
    detailNota.value = res.data.data // Data sudah termasuk pelanggan dan detilPesan
    showModalDetail.value = true
  } catch (err) {
    alert("Gagal memuat detail pesanan")
  }
}
// 8. Hitung Total dari Detail Nota (Fallback jika total_bayar tidak ada)
const calculateTotal = (items) => {
  if (!items) return 0
  return items.reduce((acc, item) => acc + (item.harga * item.jumlah), 0)
}

onMounted(() => loadInitialData())
</script>

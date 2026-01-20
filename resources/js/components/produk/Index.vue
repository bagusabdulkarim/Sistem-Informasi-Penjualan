<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Data Produk</h1>
        <p class="text-slate-500 text-sm">Kelola katalog produk dan stok inventaris.</p>
      </div>
      <button @click="openModalAdd" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg flex items-center justify-center gap-2">
        <span>📦</span> Tambah Produk
      </button>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="animate-spin inline-block w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full"></div>
      <p class="text-slate-500 mt-2">Memuat produk...</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div v-for="item in produk" :key="item.id_produk" class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-md transition group">
        <div class="h-48 bg-slate-100 relative overflow-hidden">
          <img v-if="item.gambar" :src="'/storage/produk/' + item.gambar" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
          <div v-else class="w-full h-full flex items-center justify-center text-slate-400">
            <span class="text-4xl">🖼️</span>
          </div>
          <div class="absolute top-2 right-2 bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-[10px] font-bold shadow-sm">
            STOK: {{ item.stock }}
          </div>
        </div>

        <div class="p-4">
          <h3 class="font-bold text-slate-800 truncate">{{ item.nm_produk }}</h3>
          <p class="text-indigo-600 font-black mt-1 text-lg">Rp {{ Number(item.harga).toLocaleString('id-ID') }}</p>
          <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">{{ item.id_produk }} | Satuan: {{ item.satuan }}</p>

          <div class="flex gap-2 mt-4">
            <button @click="openModalEdit(item)" class="flex-1 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold hover:bg-amber-100 hover:text-amber-600 transition">EDIT</button>
            <button @click="handleDelete(item.id_produk)" class="flex-1 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold hover:bg-red-100 hover:text-red-600 transition">HAPUS</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
      <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in duration-300">
        <div class="px-8 py-5 bg-indigo-600 text-white flex justify-between items-center">
          <h3 class="text-lg font-bold">{{ isEdit ? 'Update Produk' : 'Produk Baru' }}</h3>
          <button @click="showModal = false">✕</button>
        </div>

        <form @submit.prevent="submitForm" class="p-8 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
              <label class="text-xs font-bold text-slate-500 uppercase">ID Produk</label>
              <input v-model="form.id_produk" :disabled="isEdit" type="text" class="w-full mt-1 p-3 border rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 disabled:bg-slate-100" placeholder="PR001" required>
            </div>
            <div class="col-span-2">
              <label class="text-xs font-bold text-slate-500 uppercase">Nama Produk</label>
              <input v-model="form.nm_produk" type="text" class="w-full mt-1 p-3 border rounded-xl outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Nama barang..." required>
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase">Satuan</label>
              <input v-model="form.satuan" type="text" class="w-full mt-1 p-3 border rounded-xl outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Pcs/Kg/Box" required>
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase">Harga (Rp)</label>
              <input v-model="form.harga" type="number" class="w-full mt-1 p-3 border rounded-xl outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase">Stok Awal</label>
              <input v-model="form.stock" type="number" class="w-full mt-1 p-3 border rounded-xl outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase">Foto Produk</label>
              <input type="file" @change="handleFileUpload" class="w-full mt-1 text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept="image/*">
            </div>
          </div>

          <div class="pt-4 flex gap-3">
            <button type="button" @click="showModal = false" class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold">Batal</button>
            <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200">
              {{ isEdit ? 'Simpan Perubahan' : 'Simpan Produk' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import axios from 'axios'

const produk = ref([])
const loading = ref(true)
const showModal = ref(false)
const isEdit = ref(false)
const selectedFile = ref(null)

const form = reactive({
  id_produk: '',
  nm_produk: '',
  satuan: '',
  harga: 0,
  stock: 0
})

const getProduk = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/produk')
    produk.value = response.data.data
  } finally {
    loading.value = false
  }
}

const handleFileUpload = (event) => {
  selectedFile.value = event.target.files[0]
}

const openModalAdd = () => {
  isEdit.value = false
  Object.assign(form, { id_produk: '', nm_produk: '', satuan: '', harga: 0, stock: 0 })
  selectedFile.value = null
  showModal.value = true
}

const openModalEdit = (item) => {
  isEdit.value = true
  Object.assign(form, item)
  selectedFile.value = null
  showModal.value = true
}

const submitForm = async () => {
  const formData = new FormData()
  formData.append('id_produk', form.id_produk)
  formData.append('nm_produk', form.nm_produk)
  formData.append('satuan', form.satuan)
  formData.append('harga', form.harga)
  formData.append('stock', form.stock)

  if (selectedFile.value) {
    formData.append('gambar', selectedFile.value)
  }

  try {
    let response;
    if (isEdit.value) {
      // PENTING: Untuk EDIT yang ada filenya, gunakan POST + _method=PUT
      formData.append('_method', 'PUT')
      response = await axios.post(`/api/produk/${form.id_produk}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      // Untuk TAMBAH
      response = await axios.post('/api/produk', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    if (response.data.success) {
      alert(response.data.message)
      showModal.value = false
      getProduk()
    }
  } catch (err) {
    // Diagnosa Error
    if (err.response && err.response.status === 422) {
      // Jika error validasi (misal: gambar terlalu besar atau field kosong)
      console.log("Error Validasi:", err.response.data)
      alert("Validasi Gagal: " + JSON.stringify(err.response.data.errors))
    } else {
      console.error("Error Sistem:", err.response)
      alert("Terjadi kesalahan sistem. Cek konsol browser.")
    }
  }
}

const handleDelete = async (id) => {
  if (confirm("Hapus produk ini?")) {
    await axios.delete(`/api/produk/${id}`)
    getProduk()
  }
}

onMounted(() => getProduk())
</script>

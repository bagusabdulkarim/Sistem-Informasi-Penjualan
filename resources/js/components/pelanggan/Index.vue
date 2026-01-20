<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Data Pelanggan</h1>
        <p class="text-slate-500 text-sm">Kelola informasi lengkap pelanggan Anda di sini.</p>
      </div>
      <button
        @click="openModalAdd"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-indigo-200 flex items-center justify-center gap-2"
      >
        <span>➕</span> Tambah Pelanggan
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Pelanggan</p>
        <p class="text-2xl font-black text-slate-800">{{ pelanggan.length }}</p>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
              <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">ID</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Pelanggan</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase hidden md:table-cell">Kontak & Alamat</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in pelanggan" :key="item.id_pelanggan" class="hover:bg-slate-50/50 transition">
              <td class="px-6 py-4">
                <span class="text-xs font-mono bg-indigo-50 text-indigo-600 px-2 py-1 rounded font-bold">
                  {{ item.id_pelanggan }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="font-bold text-slate-700">{{ item.nm_pelanggan }}</div>
                <div class="text-xs text-slate-400 md:hidden">{{ item.telepon }}</div>
              </td>
              <td class="px-6 py-4 hidden md:table-cell">
                <div class="text-sm text-slate-600">{{ item.email }}</div>
                <div class="text-xs text-slate-400">{{ item.alamat }} | {{ item.telepon }}</div>
              </td>
              <td class="px-6 py-4 text-center">
                <div class="flex justify-center gap-2">
                  <button @click="openModalEdit(item)" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition">✏️</button>
                  <button @click="handleDelete(item.id_pelanggan)" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">🗑️</button>
                </div>
              </td>
            </tr>
            <tr v-if="pelanggan.length === 0 && !loading">
              <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data pelanggan.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- modal tambah & edit pelanggan -->
    <div v-if="showModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
      <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in duration-300">
        <div class="px-8 py-6 bg-indigo-600 text-white flex justify-between items-center">
          <h3 class="text-xl font-bold">{{ isEdit ? 'Edit Pelanggan' : 'Tambah Pelanggan' }}</h3>
          <button @click="showModal = false" class="hover:rotate-90 transition-transform duration-300">✕</button>
        </div>

        <form @submit.prevent="submitForm" class="p-8 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="text-xs font-bold uppercase text-slate-500 ml-1">ID Pelanggan</label>
              <input v-model="form.id_pelanggan" type="text" :disabled="isEdit" class="w-full mt-1 p-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 disabled:bg-slate-100" placeholder="PLG001" required>
            </div>
            <div class="md:col-span-2">
              <label class="text-xs font-bold uppercase text-slate-500 ml-1">Nama Lengkap</label>
              <input v-model="form.nm_pelanggan" type="text" class="w-full mt-1 p-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Masukkan nama..." required>
            </div>
            <div>
              <label class="text-xs font-bold uppercase text-slate-500 ml-1">Telepon</label>
              <input v-model="form.telepon" type="text" class="w-full mt-1 p-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500" placeholder="08..." required>
            </div>
            <div>
              <label class="text-xs font-bold uppercase text-slate-500 ml-1">Email</label>
              <input v-model="form.email" type="email" class="w-full mt-1 p-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500" placeholder="email@contoh.com" required>
            </div>
            <div class="md:col-span-2">
              <label class="text-xs font-bold uppercase text-slate-500 ml-1">Alamat</label>
              <textarea v-model="form.alamat" rows="2" class="w-full mt-1 p-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Alamat lengkap..."></textarea>
            </div>
          </div>

          <div class="pt-4 flex gap-3">
            <button type="button" @click="showModal = false" class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition">Batal</button>
            <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
              {{ isEdit ? 'Simpan Perubahan' : 'Simpan Data' }}
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
// pelanggan state
const pelanggan = ref([])
const loading = ref(true)
const showModal = ref(false)
const isEdit = ref(false)
// formulir pelanggan
const form = reactive({
  id_pelanggan: '',
  nm_pelanggan: '',
  alamat: '',
  telepon: '',
  email: ''
})
// fungsi ambil data pelanggan
const getPelanggan = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/pelanggan')
    pelanggan.value = response.data.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}
// fungsi buka modal tambah & edit
const openModalAdd = () => {
  isEdit.value = false
  Object.assign(form, { id_pelanggan: '', nm_pelanggan: '', alamat: '', telepon: '', email: '' })
  showModal.value = true
}
// fungsi buka modal edit dengan data terisi
const openModalEdit = (data) => {
  isEdit.value = true
  Object.assign(form, data)
  showModal.value = true
}
// fungsi submit form tambah & edit
const submitForm = async () => {
  try {
    if (isEdit.value) {
      await axios.put(`/api/pelanggan/${form.id_pelanggan}`, form)
    } else {
      await axios.post('/api/pelanggan', form)
    }
    showModal.value = false
    getPelanggan()
  } catch (err) {
    alert("Terjadi kesalahan simpan data.")
  }
}
// fungsi hapus pelanggan
const handleDelete = async (id) => {
  if (confirm("Hapus data pelanggan ini?")) {
    try {
      await axios.delete(`/api/pelanggan/${id}`)
      getPelanggan()
    } catch (err) {
      alert("Gagal menghapus data.")
    }
  }
}

onMounted(() => getPelanggan())
</script>

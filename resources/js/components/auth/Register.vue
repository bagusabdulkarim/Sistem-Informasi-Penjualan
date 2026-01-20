<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-100">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
      <h2 class="text-2xl font-bold text-indigo-600 text-center mb-2">Daftar Akun Baru</h2>
      <p class="text-center text-slate-500 text-sm mb-6">Silakan lengkapi data di bawah ini</p>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
          <input v-model="form.name" type="text" class="w-full mt-1 p-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-400" placeholder="Nama Anda" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input v-model="form.email" type="email" class="w-full mt-1 p-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-400" placeholder="email@contoh.com" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <input v-model="form.password" type="password" class="w-full mt-1 p-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-400" placeholder="Minimal 6 karakter" required>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">
          Daftar Sekarang
        </button>
      </form>

      <div class="mt-6 text-center">
        <button @click="$emit('switch-view', 'login')" class="text-sm text-indigo-600 hover:underline">
          Sudah punya akun? Login di sini
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import axios from 'axios'

const emit = defineEmits(['register-success', 'switch-view'])
const form = reactive({ name: '', email: '', password: '' })

const handleRegister = async () => {
  try {
    // Memanggil AuthController@register yang sudah kamu siapkan di Laravel
    const response = await axios.post('/api/register', form)
    alert(response.data.message)
    emit('register-success') // Pindah ke halaman login setelah sukses
  } catch (error) {
    if (error.response && error.response.status === 422) {
      alert("Email sudah digunakan atau data tidak valid!")
    } else {
      alert("Gagal mendaftar, coba lagi.")
    }
  }
}
</script>

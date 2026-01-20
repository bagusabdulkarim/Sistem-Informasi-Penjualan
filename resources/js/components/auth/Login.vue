<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-100 p-6">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-slate-200">
      <h2 class="text-2xl font-bold text-indigo-600 text-center mb-6">Login SIP</h2>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input v-model="form.email" type="email" class="w-full mt-1 p-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-400" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <input v-model="form.password" type="password" class="w-full mt-1 p-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-400" required>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
          Masuk
        </button>

        <div class="mt-6 text-center border-t pt-4">
          <p class="text-sm text-gray-600">
            Belum punya akun?
            <button
              type="button"
              @click="$emit('switch-view')"
              class="text-indigo-600 font-bold hover:underline"
            >
              Daftar Sekarang
            </button>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, defineEmits } from 'vue'
import axios from 'axios'

// Kita perlu mendefinisikan 'switch-view' agar bisa didengar oleh App.vue
const emit = defineEmits(['login-success', 'switch-view'])
const form = reactive({ email: '', password: '' })

const handleLogin = async () => {
  try {
    const response = await axios.post('/api/login', form)
    if (response.data.access_token) {
      localStorage.setItem('auth_token', response.data.access_token)
      emit('login-success')
    }
  } catch (error) {
    alert("Email atau Password salah!")
  }
}
</script>

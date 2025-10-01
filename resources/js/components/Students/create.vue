<template>
  <div class="bg-bg text-text transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8 text-center">
        <a :href="indexRoute"
           class="flex items-center gap-2 text-text hover:text-blue-500 transition-colors mb-4 group">
          <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
          Back to Students
        </a>
        <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl">
          <i class="fas fa-user-graduate text-white text-3xl"></i>
        </div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-green-500 to-blue-600 bg-clip-text text-transparent mb-2">
          Register New Student
        </h1>
        <p class="text-lg opacity-75">Add a student to the system</p>
      </div>

      <!-- Form -->
      <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <form @submit.prevent="submitForm" class="p-6 space-y-6">
          <!-- Name -->
          <div>
            <label class="block font-semibold mb-2">Full Name *</label>
            <input v-model="form.name"
                   type="text"
                   placeholder="Enter student name"
                   required
                   maxlength="255"
                   class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-blue-500"/>
          </div>

          <!-- Email -->
          <div>
            <label class="block font-semibold mb-2">Email *</label>
            <input v-model="form.email"
                   type="email"
                   placeholder="student@example.com"
                   required
                   maxlength="255"
                   class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-green-500"/>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4 border-t border-border">
            <button type="submit"
                    :disabled="isSubmitting"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fas fa-plus" :class="{'fa-spin': isSubmitting}"></i>
              {{ isSubmitting ? 'Saving...' : 'Save Student' }}
            </button>
            <button type="button"
                    @click="resetForm"
                    class="px-6 py-3 border-2 border-border rounded-xl hover:bg-gray-100">
              Reset
            </button>
          </div>
        </form>
      </div>
    </div>
    <toast-container />
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const props = defineProps({
  storeRoute: { type: String, required: true },
  indexRoute: { type: String, required: true },
  csrfToken: { type: String, required: true }
})

const toast = useToast()
const isSubmitting = ref(false)

const form = reactive({
  name: '',
  email: ''
})

const resetForm = () => {
  form.name = ''
  form.email = ''
}

const submitForm = async () => {
  if (!form.name || !form.email) {
    toast.error('Name and Email are required.', { timeout: 4000 })
    return
  }
  isSubmitting.value = true
  try {
    const { data } = await axios.post(props.storeRoute, form, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })
    if (data.success) {
      toast.success('Student registered successfully!', { timeout: 3000 })
      setTimeout(() => { window.location.href = props.indexRoute }, 1200)
    }
  } catch (err) {
    console.error(err.response?.data || err)
    toast.error('Failed to register student. Try again.', { timeout: 4000 })
  } finally {
    isSubmitting.value = false
  }
}
</script>

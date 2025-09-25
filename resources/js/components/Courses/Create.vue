<template>
  <div class="bg-bg text-text transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 py-8">

      <!-- Header -->
      <div class="mb-8 text-center">
        <a :href="props.indexRoute"
           class="flex items-center gap-2 text-text hover:text-blue-500 transition-colors mb-4 group">
          <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
          Back to Courses
        </a>
        <div
          class="w-20 h-20 bg-gradient-to-br from-green-400 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl">
          <i class="fas fa-plus text-white text-3xl"></i>
        </div>
        <h1
          class="text-4xl font-bold bg-gradient-to-r from-green-500 to-blue-600 bg-clip-text text-transparent mb-2">
          Create New Course
        </h1>
        <p class="text-lg opacity-75">Start building your amazing learning experience</p>
      </div>

      <!-- Form -->
      <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <form @submit.prevent="submitForm" class="p-6 space-y-6">

          <!-- Title -->
          <div>
            <label class="block font-semibold mb-2">Course Title *</label>
            <input v-model="form.title"
                    type="text"
                    placeholder="e.g., Advanced Web Development with Vue.js"
                    maxlength="255"
                    required
                    @input="updateTitleStats"
                    class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-blue-500 focus:ring focus:ring-blue-300"/>
            <p v-if="errors.title" class="text-red-500 text-sm mt-1">{{ errors.title[0] }}</p>       
            <div class="flex justify-between mt-1 text-xs opacity-75">
              <span>Clear titles attract more students</span>
              <span>{{ form.title.length }}/255</span>
            </div>
          </div>

          <!-- Category -->
          <div>
            <label class="block font-semibold mb-2">Category *</label>
            <input v-model="form.category"
                  type="text"
                  placeholder="Science, Design, Programming..."
                  required
                  class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-green-500"/>
            <p v-if="errors.category" class="text-red-500 text-sm mt-1">{{ errors.category[0] }}</p>
          </div>

          <!-- Description -->
          <div>
            <label class="block font-semibold mb-2">Description</label>
            <textarea v-model="form.description"
                      rows="6"
                      placeholder="What will students learn?"
                      maxlength="1000"
                      @input="updateDescriptionStats"
                      class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text resize-none"></textarea>
            <p v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description[0] }}</p>
            <div class="text-xs flex gap-4 mt-2">
              <span>{{ wordCount }} words</span>
              <span>{{ characterCount }} chars</span>
              <span>{{ readingTime }} min read</span>
            </div>
          </div>

          <!-- Cover Image -->
          <div>
            <label class="block font-semibold mb-2">Cover Image</label>
            <div class="flex gap-2">
              <input v-model="form.cover_url"
                     type="url"
                     placeholder="https://example.com/image.jpg"
                     class="flex-1 px-4 py-3 border-2 border-border rounded-xl bg-bg text-text"/>
              <button type="button"
                      @click="generatePlaceholderImage"
                      class="px-4 py-2 bg-yellow-200 rounded-xl hover:bg-yellow-300 transition">
                <i class="fas fa-wand-magic-sparkles"></i>
              </button>
            </div>
            <div v-if="form.cover_url" class="mt-2">
              <img :src="form.cover_url"
                   class="w-32 h-20 rounded-lg object-cover border"/>
            </div>
          </div>

          <!-- Publish -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-border space-y-3">
            <div class="flex items-center justify-between">
              <label class="font-semibold">Publish Immediately</label>
              <input type="checkbox" v-model="form.is_published" true-value="1" false-value="0"/>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4 border-t border-border">
            <button type="submit"
                    :disabled="isSubmitting"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fas fa-plus" :class="{'fa-spin': isSubmitting}"></i>
              {{ isSubmitting ? 'Creating...' : 'Create Course' }}
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

    <!-- Toast Container -->
    <toast-container />
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'
import 'vue-toastification/dist/index.css'

const props = defineProps({
  storeRoute: { type: String, required: true },
  indexRoute: { type: String, required: true },
  csrfToken: { type: String, required: true }
})

const form = reactive({
  title: '',
  category: '',
  description: '',
  cover_url: '',
  is_published: '0'
})

const errors = reactive({})
const isSubmitting = ref(false)
const wordCount = ref(0)
const characterCount = ref(0)
const readingTime = ref(0)

const toast = useToast()

const updateTitleStats = () => {}

const updateDescriptionStats = () => {
  const text = form.description
  characterCount.value = text.length
  wordCount.value = text.trim() ? text.trim().split(/\s+/).length : 0
  readingTime.value = Math.ceil(wordCount.value / 200)
}

const generatePlaceholderImage = () => {
  const colors = ['4f46e5', '10b981', 'f59e0b', 'ef4444', '8b5cf6']
  const color = colors[Math.floor(Math.random() * colors.length)]
  form.cover_url = `https://via.placeholder.com/800x400/${color}/ffffff?text=${encodeURIComponent(form.title || 'Course+Image')}`
}

const resetForm = () => {
  Object.assign(form, { 
    title: '', 
    category: '', 
    description: '', 
    cover_url: '', 
    is_published: '0' 
  })
  Object.keys(errors).forEach(key => errors[key] = null)
}

const submitForm = async () => {
  isSubmitting.value = true
  Object.keys(errors).forEach(key => errors[key] = null)

  try {
    await axios.post(props.storeRoute, form, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })
    toast.success('Course created successfully!', { timeout: 3000 })
    resetForm()
    setTimeout(() => window.location.href = props.indexRoute, 1000)
  } catch (err) {
    if (err.response?.status === 422) {
      Object.assign(errors, err.response.data.errors)
      toast.error('Please fix the validation errors.', { timeout: 4000 })
    } else {
      toast.error('Failed to create course. Try again.', { timeout: 4000 })
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

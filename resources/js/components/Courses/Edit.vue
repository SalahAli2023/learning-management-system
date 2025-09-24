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
          <i class="fas fa-edit text-white text-3xl"></i>
        </div>
        <h1
          class="text-4xl font-bold bg-gradient-to-r from-green-500 to-blue-600 bg-clip-text text-transparent mb-2">
          Edit Course
        </h1>
        <p class="text-lg opacity-75">Update your course details</p>
      </div>

      <!-- Form -->
      <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <form @submit.prevent="submitForm" class="p-6 space-y-6">

          <!-- Title -->
          <div>
            <label class="block font-semibold mb-2">Course Title *</label>
            <input v-model="form.title"
                   type="text"
                   placeholder="Enter course title"
                   maxlength="255"
                   required
                   @input="updateTitleStats"
                   class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-blue-500 focus:ring focus:ring-blue-300"/>
            <div class="flex justify-between mt-1 text-xs opacity-75">
              <span>Keep it clear and descriptive</span>
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
          </div>

          <!-- Description -->
          <div>
            <label class="block font-semibold mb-2">Description</label>
            <textarea v-model="form.description"
                      rows="6"
                      placeholder="Update course description"
                      maxlength="1000"
                      @input="updateDescriptionStats"
                      class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text resize-none"></textarea>
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

          <!-- Settings -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-border space-y-3">
            <div class="flex items-center justify-between">
              <label class="font-semibold">Published</label>
              <input type="checkbox" v-model="form.is_published" true-value="1" false-value="0"/>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4 border-t border-border">
            <button type="submit"
                    :disabled="isSubmitting"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fas fa-save" :class="{'fa-spin': isSubmitting}"></i>
              {{ isSubmitting ? 'Updating...' : 'Update Course' }}
            </button>
            <a :href="props.showRoute"
              class="px-6 py-3 border-2 border-border rounded-xl hover:bg-gray-100">
              Preview
            </a>
            <a :href="props.indexRoute"
              class="px-6 py-3 border-2 border-border rounded-xl hover:bg-gray-100">
              Cancel
            </a>
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
  course: { type: Object, required: true },
  updateRoute: { type: String, required: true },
  showRoute: { type: String, required: true },
  indexRoute: { type: String, required: true },
  csrfToken: { type: String, required: true }
})

const form = reactive({
  title: props.course.title || '',
  category: props.course.category || '',
  description: props.course.description || '',
  cover_url: props.course.cover_url || '',
  is_published: props.course.is_published ? '1' : '0'
})

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

const submitForm = async () => {
  if (!form.title || !form.category) {
    toast.error('Title and Category are required.', { timeout: 4000 })
    return
  }
  isSubmitting.value = true
  try {
    const { data } = await axios.put(props.updateRoute, form, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })

    if (data.success) {
      toast.success(data.message || 'Course updated successfully!', { timeout: 3000 })
      setTimeout(() => { window.location.href = props.indexRoute }, 1200)
    }
  } catch (err) {
    console.error(err.response?.data || err)
    toast.error('Failed to update course. Try again.', { timeout: 4000 })
  } finally {
    isSubmitting.value = false
  }
}
</script>

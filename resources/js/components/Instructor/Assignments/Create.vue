<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 py-8">
      
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="indexRoute" class="text-text hover:text-blue-500 flex items-center gap-1">
          <i class="fas fa-arrow-left"></i>
          Back to Assignments
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-blue-500 font-medium">New Assignment</span>
      </nav>

      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-green-500 to-blue-600 bg-clip-text text-transparent">
            <span class="text-amber-50">🆕</span> Create Assignment
          </h2>
          <p class="text-sm opacity-75 mt-1">Course: <strong>{{ course.title }}</strong></p>
        </div>
      </div>

      <!-- Create Form -->
      <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-border">
          <h3 class="text-xl font-semibold flex items-center gap-2">
            <i class="fas fa-plus-circle text-green-500"></i>
            Assignment Details
          </h3>
        </div>

        <form @submit.prevent="createAssignment" class="p-6">
          <!-- Title -->
          <div class="mb-6">
            <label class="block text-sm font-semibold mb-3 flex items-center gap-2">
              <i class="fas fa-heading text-blue-500"></i>
              Assignment Title *
            </label>
            <input
              v-model="form.title"
              type="text"
              class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
              placeholder="Enter assignment title..."
              required
              maxlength="255"
            >
            <div class="text-right text-sm text-gray-500 mt-1">
              {{ form.title?.length || 0 }}/255 characters
            </div>
          </div>

          <!-- Description -->
          <div class="mb-6">
            <label class="block text-sm font-semibold mb-3 flex items-center gap-2">
              <i class="fas fa-align-left text-green-500"></i>
              Description
            </label>
            <textarea
              v-model="form.description"
              rows="4"
              class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
              placeholder="Describe the assignment requirements..."
              maxlength="1000"
            ></textarea>
            <div class="text-right text-sm text-gray-500 mt-1">
              {{ form.description?.length || 0 }}/1000 characters
            </div>
          </div>

          <!-- Due Date -->
          <div class="mb-8">
            <label class="block text-sm font-semibold mb-3 flex items-center gap-2">
              <i class="fas fa-calendar-day text-orange-500"></i>
              Due Date
            </label>
            <input
              v-model="form.due_date"
              type="datetime-local"
              class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
              :min="minDueDate"
            >
            <p class="text-sm text-gray-500 mt-2">
              <i class="fas fa-info-circle text-blue-500 mr-1"></i>
              Leave empty if there's no due date
            </p>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end gap-3 pt-6 border-t border-border">
            <a :href="indexRoute" 
               class="px-6 py-3 border-2 border-border text-text rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all font-semibold flex items-center gap-2">
              <i class="fas fa-times"></i>
              Cancel
            </a>
            
            <button
              type="submit"
              :disabled="isSubmitting || !form.title"
              :class="[
                'px-8 py-3 rounded-xl font-semibold transition-all flex items-center gap-2',
                isSubmitting || !form.title
                  ? 'bg-gray-400 text-gray-200 cursor-not-allowed'
                  : 'bg-gradient-to-r from-green-500 to-blue-600 text-white hover:shadow-xl transform hover:scale-105'
              ]"
            >
              <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
              <i v-else class="fas fa-plus"></i>
              {{ isSubmitting ? 'Creating...' : 'Create Assignment' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useToast } from 'vue-toastification'
import axios from 'axios'

const props = defineProps({
  course: Object,
  storeRoute: String,
  indexRoute: String,
  csrfToken: String
})

const toast = useToast()
const isSubmitting = ref(false)

const form = reactive({
  title: '',
  description: '',
  due_date: ''
})

// Computed properties
const minDueDate = computed(() => {
  const now = new Date()
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset())
  return now.toISOString().slice(0, 16)
})

// Methods
const createAssignment = async () => {
  if (!form.title.trim()) {
    toast.error('Please enter an assignment title.')
    return
  }

  isSubmitting.value = true

  try {
    const response = await axios.post(props.storeRoute, form, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })

    if (response.data.success) {
      toast.success('Assignment created successfully!')
      // Redirect to assignments list
      setTimeout(() => {
        window.location.href = props.indexRoute
      }, 1500)
    }
  } catch (err) {
    console.error('Create error:', err)
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors
      Object.values(errors).forEach(errorMessages => {
        errorMessages.forEach(message => toast.error(message))
      })
    } else {
      toast.error('Failed to create assignment. Please try again.')
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
<template>
  <div class="bg-bg text-text transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 py-8">

      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="indexRoute" class="text-text hover:text-blue-500 flex items-center gap-1">
          <i class="fas fa-home"></i>
          Lessons
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-blue-500 font-medium">Create New Lesson</span>
      </nav>

      <!-- Header -->
      <div class="mb-8 text-center">
        <div
          class="w-20 h-20 bg-gradient-to-br from-green-400 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl">
          <i class="fas fa-plus text-white text-3xl"></i>
        </div>
        <h1
          class="text-4xl font-bold bg-gradient-to-r from-green-500 to-blue-600 bg-clip-text text-transparent mb-2">
          Create New Lesson
        </h1>
        <p class="text-lg opacity-75">Build engaging learning experiences for your students</p>
      </div>

      <!-- Form -->
      <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <form @submit.prevent="submitForm" enctype="multipart/form-data" class="p-6 space-y-6">

          <!-- Title -->
          <div>
            <label class="block font-semibold mb-2">Lesson Title *</label>
            <input v-model="form.title"
                  type="text"
                  placeholder="e.g., Mastering Vue.js Components"
                  maxlength="60"
                  required
                  @input="validateTitle"
                  class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-blue-500 focus:ring focus:ring-blue-300"/>
            <div class="flex justify-between mt-1 text-xs opacity-75">
              <span>Make it catchy and descriptive</span>
              <span :class="titleValid ? 'text-green-500' : 'text-red-500'">
                {{ form.title.length }}/60 {{ !titleValid ? '- Minimum 3 characters' : '' }}
              </span>
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="block font-semibold mb-2">Description</label>
            <textarea v-model="form.description"
                      rows="4"
                      placeholder="What will students learn in this lesson? Be specific and engaging..."
                      maxlength="500"
                      @input="updateDescriptionStats"
                      class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text resize-none"></textarea>
            <div class="text-xs flex gap-4 mt-2 opacity-75">
              <span>{{ wordCount }} words</span>
              <span>{{ characterCount }} chars</span>
              <span>{{ readingTime }} min read</span>
            </div>
          </div>

          <!-- Duration & Settings Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Duration -->
            <div>
              <label class="block font-semibold mb-2">Duration (minutes) *</label>
              <div class="relative">
                <input v-model.number="form.duration"
                       type="number"
                       min="1"
                       max="480"
                       required
                       class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text pr-12"/>
                <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                  <i class="fas fa-hourglass-half"></i>
                </div>
              </div>
              <p class="text-xs opacity-75 mt-1">{{ formatDuration(form.duration) }}</p>
            </div>

            <!-- Free Lesson -->
            <div>
              <label class="block font-semibold mb-2">Access Type</label>
              <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-border">
                <div class="flex items-center justify-between">
                  <div>
                    <span class="font-semibold">Free Lesson</span>
                    <p class="text-xs opacity-75">Available to all students</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input v-model="form.is_free"
                           true-value="1"
                           false-value="0"
                           type="checkbox"
                           class="sr-only"/>
                    <div :class="[
                      'w-14 h-7 rounded-full transition-all duration-300',
                      form.is_free === '1' 
                        ? 'bg-green-500' 
                        : 'bg-gray-300 dark:bg-gray-600'
                    ]">
                      <div :class="[
                        'absolute top-1 left-1 bg-white w-5 h-5 rounded-full transition-transform duration-300',
                        form.is_free === '1' ? 'transform translate-x-7' : ''
                      ]"></div>
                    </div>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- File Upload -->
          <div>
            <label class="block font-semibold mb-2">Lesson Content</label>
            <div class="border-2 border-dashed border-border rounded-xl p-6 text-center transition-all duration-300 hover:border-blue-400 hover:bg-blue-50/50 group">
              <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3 group-hover:text-blue-500 transition-colors"></i>
              
              <label class="cursor-pointer">
                <input type="file"
                       @change="handleFileChange"
                       class="hidden"
                       accept=".pdf,.doc,.docx,.mp4,.mov,.avi,.jpg,.jpeg,.png"/>
                <div class="space-y-2">
                  <p class="text-lg font-semibold">Upload Lesson File</p>
                  <p class="text-sm opacity-75">Drag & drop or click to browse</p>
                  <p class="text-xs opacity-60">Supports: PDF, DOC, MP4, Images (Max 50MB)</p>
                </div>
              </label>

              <!-- Selected File Info -->
              <div v-if="form.file" class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <i class="fas fa-file text-green-500"></i>
                    <div>
                      <p class="font-semibold text-green-800 dark:text-green-300">{{ form.file.name }}</p>
                      <p class="text-xs text-green-600 dark:text-green-400">{{ formatFileSize(form.file.size) }}</p>
                    </div>
                  </div>
                  <button @click="removeFile"
                          class="text-red-500 hover:text-red-700 transition-colors">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex gap-3 pt-6 border-t border-border">
            <button type="submit"
                    :disabled="isSubmitting"
                    :class="[
                      'flex-1 px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-2',
                      isSubmitting 
                        ? 'bg-gray-400 cursor-not-allowed' 
                        : 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg hover:shadow-xl'
                    ]">
              <i class="fas fa-plus" :class="{ 'fa-spin': isSubmitting }"></i>
              {{ isSubmitting ? 'Creating Lesson...' : 'Create Lesson' }}
            </button>
            
            <a :href="props.indexRoute"
               class="px-6 py-3 border-2 border-border text-text rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all duration-300 text-center">
              Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const props = defineProps({
  storeRoute: {
    type: String,
    required: true
  },
  indexRoute: {
    type: String,
    required: true
  },
  csrfToken: {
    type: String,
    required: true
  },
  courseId: {
    type: [String, Number],
    required: true
  }
})

// Form data
const form = reactive({
  title: '',
  description: '',
  duration: 30,
  is_free: '0',
  file: null
})

// UI state
const isSubmitting = ref(false)
const titleValid = ref(false)
const errors = reactive({})

const toast = useToast()

// Computed properties
const wordCount = computed(() => {
  return form.description.trim() ? form.description.trim().split(/\s+/).length : 0
})

const characterCount = computed(() => {
  return form.description.length
})

const readingTime = computed(() => {
  return Math.ceil(wordCount.value / 200) || 1
})

// Methods
const validateTitle = () => {
  titleValid.value = form.title.trim().length >= 3
}

const updateDescriptionStats = () => {
  // Stats are computed automatically
}

const formatDuration = (minutes) => {
  if (!minutes) return ''
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return hours > 0 ? `${hours}h ${mins}m` : `${mins} minutes`
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file size
    if (file.size > 50 * 1024 * 1024) {
      toast.error('File size must be less than 50MB')
      return
    }
    
    // Validate file type
    const allowedTypes = ['.pdf', '.doc', '.docx', '.mp4', '.mov', '.avi', '.jpg', '.jpeg', '.png']
    const fileExtension = '.' + file.name.split('.').pop().toLowerCase()
    
    if (!allowedTypes.includes(fileExtension)) {
      toast.error('Please select a valid file type')
      return
    }
    
    form.file = file
    toast.success('File selected successfully!')
  }
}

const removeFile = () => {
  form.file = null
}

const submitForm = async () => {
  isSubmitting.value = true
  Object.keys(errors).forEach(key => errors[key] = null)

  try {
    const formData = new FormData()
    
    formData.append('_token', props.csrfToken)
    formData.append('title', form.title)
    formData.append('description', form.description)
    formData.append('duration', form.duration)
    formData.append('is_free', form.is_free)
    formData.append('course_id', props.courseId)
    
    if (form.file) {
      formData.append('file', form.file)
    }

    const response = await axios.post(props.storeRoute, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      }
    })

    toast.success('Lesson created successfully!', { timeout: 3000 })
    setTimeout(() => {
      window.location.href = props.indexRoute
    }, 1000)

  } catch (err) {
    console.error('Creation error:', err)
    
    if (err.response?.status === 422) {
      Object.assign(errors, err.response.data.errors)
      toast.error('Please fix the validation errors.', { timeout: 4000 })
    } else {
      toast.error(err.response?.data?.message || 'Failed to create lesson. Please try again.', { timeout: 4000 })
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
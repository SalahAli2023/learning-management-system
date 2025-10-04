<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 py-8">
      
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="assignmentsRoute" class="text-text hover:text-blue-500 flex items-center gap-1">
          <i class="fas fa-arrow-left"></i>
          Back to Assignments
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-blue-500 font-medium">{{ assignment.title }}</span>
      </nav>

      <!-- Assignment Header -->
      <div class="bg-card border border-border rounded-2xl shadow-xl overflow-hidden mb-8">
        <div class="p-8">
          <div class="flex flex-col lg:flex-row justify-between items-start gap-6">
            <div class="flex-1">
              <h1 class="text-3xl font-bold mb-4">{{ assignment.title }}</h1>
              
              <!-- Due Date & Status -->
              <div class="flex flex-wrap gap-4 mb-4">
                <div class="flex items-center gap-2" :class="dueDateClass">
                  <i class="fas fa-calendar"></i>
                  <span class="font-semibold">Due: {{ formattedDueDate }}</span>
                  <span v-if="isOverdue" class="text-xs bg-red-500 text-white px-2 py-1 rounded">
                    Overdue
                  </span>
                </div>

                <!-- Submission Status -->
                <div v-if="submission" class="flex items-center gap-2">
                  <span :class="statusBadgeClass" class="text-sm font-semibold">
                    <i :class="statusIcon" class="mr-1"></i>
                    {{ statusText }}
                  </span>
                </div>
              </div>

              <!-- Description -->
              <div class="prose dark:prose-invert max-w-none mb-6">
                <p class="text-gray-600 dark:text-gray-400 text-lg">
                  {{ assignment.description }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Submission Form -->
      <div v-if="!submission" class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 border-b border-border">
          <h2 class="text-xl font-semibold flex items-center gap-2">
            <i class="fas fa-paper-plane text-green-500"></i>
            Submit Assignment
          </h2>
        </div>

        <form @submit.prevent="submitAssignment" class="p-6">
          <!-- Text Submission -->
          <div class="mb-6">
            <label class="block text-sm font-semibold mb-3">Your Submission Text</label>
            <textarea
              v-model="form.submission_text"
              rows="8"
              class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
              placeholder="Type your assignment submission here..."
              :maxlength="5000"
            ></textarea>
            <div class="text-right text-sm text-gray-500 mt-1">
              {{ form.submission_text?.length || 0 }}/5000 characters
            </div>
          </div>

          <!-- File Upload -->
          <div class="mb-6">
            <label class="block text-sm font-semibold mb-3">Upload File (Optional)</label>
            <div class="border-2 border-dashed border-border rounded-xl p-6 text-center transition-all"
                 :class="{
                   'border-blue-500 bg-blue-50 dark:bg-blue-900/20': isDragging,
                   'hover:border-blue-500 hover:bg-gray-50 dark:hover:bg-gray-800': !isDragging
                 }"
                 @dragover.prevent="isDragging = true"
                 @dragleave="isDragging = false"
                 @drop="handleFileDrop">
              
              <input
                type="file"
                ref="fileInput"
                @change="handleFileSelect"
                class="hidden"
                accept=".pdf,.doc,.docx,.txt,.zip,.rar,.jpg,.jpeg,.png"
              >
              
              <div class="space-y-3">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mx-auto">
                  <i class="fas fa-cloud-upload-alt text-white text-xl"></i>
                </div>
                
                <div>
                  <p class="font-semibold">Drop your file here or click to browse</p>
                  <p class="text-sm text-gray-500 mt-1">
                    Supports: PDF, Word, Text, Images, Archives (Max: 10MB)
                  </p>
                </div>
                
                <button
                  type="button"
                  @click="$refs.fileInput.click()"
                  class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                >
                  Browse Files
                </button>
              </div>
            </div>

            <!-- Selected File Preview -->
            <div v-if="selectedFile" class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <i class="fas fa-file text-green-500 text-xl"></i>
                  <div>
                    <p class="font-semibold text-green-800 dark:text-green-300">{{ selectedFile.name }}</p>
                    <p class="text-sm text-green-700 dark:text-green-400">
                      {{ formatFileSize(selectedFile.size) }}
                    </p>
                  </div>
                </div>
                <button
                  type="button"
                  @click="removeFile"
                  class="text-red-500 hover:text-red-700 transition"
                >
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Submission Button -->
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="isSubmitting || (!form.submission_text && !selectedFile)"
              :class="[
                'px-8 py-3 rounded-xl font-semibold transition-all flex items-center gap-2',
                isSubmitting || (!form.submission_text && !selectedFile)
                  ? 'bg-gray-400 text-gray-200 cursor-not-allowed'
                  : 'bg-green-500 text-white hover:bg-green-600 transform hover:scale-105'
              ]"
            >
              <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
              <i v-else class="fas fa-paper-plane"></i>
              {{ isSubmitting ? 'Submitting...' : 'Submit Assignment' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Already Submitted Message -->
      <div v-else class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-border">
          <h2 class="text-xl font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i>
            Submission Received
          </h2>
        </div>

        <div class="p-6">
          <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
              <div class="flex items-center gap-3">
                <i class="fas fa-calendar text-blue-500"></i>
                <span class="font-semibold">Submitted on:</span>
                <span>{{ formatDateTime(submission.created_at) }}</span>
              </div>
              <span :class="statusBadgeClass" class="text-sm font-semibold">
                {{ statusText }}
              </span>
            </div>

            <!-- Submission Text -->
            <div v-if="submission.submission_text" class="bg-white dark:bg-gray-800 rounded-xl p-6 border">
              <h3 class="font-semibold mb-3">Your Submission:</h3>
              <div class="prose dark:prose-invert max-w-none">
                <p class="whitespace-pre-wrap">{{ submission.submission_text }}</p>
              </div>
            </div>

            <!-- Submitted File -->
            <div v-if="submission.file_url" class="bg-white dark:bg-gray-800 rounded-xl p-6 border">
              <h3 class="font-semibold mb-3">Attached File:</h3>
              <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <i class="fas fa-file text-green-500 text-xl"></i>
                <div class="flex-1">
                  <p class="font-semibold">{{ getFileName(submission.file_url) }}</p>
                </div>
                <a :href="getDownloadRoute(submission)" 
                   class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-sm">
                  Download
                </a>
              </div>
            </div>

            <!-- Grade & Feedback -->
            <div v-if="submission.grade !== null" class="bg-green-50 dark:bg-green-900/20 rounded-xl p-6">
              <h3 class="font-semibold text-green-800 dark:text-green-300 mb-3 flex items-center gap-2">
                <i class="fas fa-star"></i>
                Grade & Feedback
              </h3>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="text-center">
                  <div class="text-3xl font-bold text-green-600">{{ submission.grade }}</div>
                  <div class="text-sm text-green-700 dark:text-green-400">Your Score</div>
                </div>
                <div class="text-center">
                  <div class="text-3xl font-bold text-blue-600">/ 100</div>
                  <div class="text-sm text-blue-700 dark:text-blue-400">Max Score</div>
                </div>
                <div class="text-center">
                  <div class="text-3xl font-bold text-purple-600">
                    {{ submission.grade }}%
                  </div>
                  <div class="text-sm text-purple-700 dark:text-purple-400">Percentage</div>
                </div>
              </div>

              <div v-if="submission.feedback" class="mt-4">
                <h4 class="font-semibold text-green-800 dark:text-green-300 mb-2">Instructor Feedback:</h4>
                <div class="prose dark:prose-invert max-w-none text-green-700 dark:text-green-400">
                  <p class="whitespace-pre-wrap">{{ submission.feedback }}</p>
                </div>
              </div>
            </div>

            <div v-else class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6">
              <div class="flex items-center gap-3">
                <i class="fas fa-clock text-blue-500 text-xl"></i>
                <div>
                  <h4 class="font-semibold text-blue-800 dark:text-blue-300">Waiting for Grade</h4>
                  <p class="text-blue-700 dark:text-blue-400 text-sm">
                    Your submission is under review. You'll receive a grade and feedback soon.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
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
  assignment: Object,
  submission: Object,
  submitRoute: String,
  assignmentsRoute: String,
  csrfToken: String
})

const toast = useToast()
const fileInput = ref(null)
const selectedFile = ref(null)
const isDragging = ref(false)
const isSubmitting = ref(false)

const form = reactive({
  submission_text: '',
  file: null
})

// Computed properties
const formattedDueDate = computed(() => {
  if (!props.assignment.due_date) return 'No due date'
  return new Date(props.assignment.due_date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
})

const dueDateClass = computed(() => {
  if (!props.assignment.due_date) return 'text-gray-500'
  
  const dueDate = new Date(props.assignment.due_date)
  const now = new Date()
  
  if (dueDate < now) return 'text-red-500'
  if (dueDate < new Date(now.getTime() + 24 * 60 * 60 * 1000)) return 'text-orange-500'
  return 'text-green-500'
})

const isOverdue = computed(() => {
  if (!props.assignment.due_date) return false
  return new Date(props.assignment.due_date) < new Date()
})

const statusText = computed(() => {
  if (!props.submission) return 'Not Submitted'
  return props.submission.grade !== null ? 'Graded' : 'Submitted'
})

const statusBadgeClass = computed(() => {
  if (!props.submission) return 'px-3 py-1 bg-gray-100 text-gray-600 rounded-full'
  return props.submission.grade !== null 
    ? 'px-3 py-1 bg-green-100 text-green-600 rounded-full' 
    : 'px-3 py-1 bg-blue-100 text-blue-600 rounded-full'
})

const statusIcon = computed(() => {
  if (!props.submission) return 'fas fa-times-circle'
  return props.submission.grade !== null ? 'fas fa-check-circle' : 'fas fa-paper-plane'
})

// Methods
const formatDateTime = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const getFileName = (fileUrl) => {
  return fileUrl.split('/').pop() || 'Download File'
}

const getDownloadRoute = (submission) => {
  return `/student/courses/${props.course.id}/assignments/${props.assignment.id}/submissions/${submission.id}/download`
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 10 * 1024 * 1024) { // 10MB
      toast.error('File size must be less than 10MB')
      return
    }
    selectedFile.value = file
    form.file = file
  }
}

const handleFileDrop = (event) => {
  isDragging.value = false
  const file = event.dataTransfer.files[0]
  if (file) {
    if (file.size > 10 * 1024 * 1024) {
      toast.error('File size must be less than 10MB')
      return
    }
    selectedFile.value = file
    form.file = file
  }
}

const removeFile = () => {
  selectedFile.value = null
  form.file = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const submitAssignment = async () => {
  if (!form.submission_text && !form.file) {
    toast.error('Please provide either submission text or a file')
    return
  }

  isSubmitting.value = true

  try {
    const formData = new FormData()
    formData.append('submission_text', form.submission_text)
    if (form.file) {
      formData.append('file', form.file)
    }

    const response = await axios.post(props.submitRoute, formData, {
      headers: {
        'X-CSRF-TOKEN': props.csrfToken,
        'Content-Type': 'multipart/form-data'
      }
    })

    if (response.data.success) {
      toast.success('Assignment submitted successfully!')
      // Redirect to assignments list
      setTimeout(() => {
        window.location.href = props.assignmentsRoute
      }, 1500)
    }
  } catch (err) {
    console.error('Submission error:', err)
    if (err.response?.status === 422) {
      toast.error('You have already submitted this assignment.')
    } else {
      toast.error('Failed to submit assignment. Please try again.')
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
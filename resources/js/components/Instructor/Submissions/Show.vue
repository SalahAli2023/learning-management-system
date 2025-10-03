<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-6xl mx-auto px-4 py-8">
      
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="submissionsRoute" class="text-text hover:text-blue-500 flex items-center gap-1">
          <i class="fas fa-arrow-left"></i>
          Back to Submissions
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-blue-500 font-medium">Grade Submission</span>
      </nav>

      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-500 to-pink-600 bg-clip-text text-transparent">
            <span class="text-amber-50">📝</span> Grade Submission
          </h2>
          <p class="text-sm opacity-75 mt-1">
            <strong>{{ assignment.title }}</strong> - {{ course.title }}
          </p>
        </div>
        
        <!-- Student Info -->
        <div class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
          <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
            {{ submission.student.name.charAt(0).toUpperCase() }}
          </div>
          <div>
            <h3 class="font-semibold text-blue-800 dark:text-blue-300">{{ submission.student.name }}</h3>
            <p class="text-blue-700 dark:text-blue-400 text-sm">{{ submission.student.email }}</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Submission Details -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Submission Info -->
          <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-border">
              <h3 class="text-xl font-semibold flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Submission Details
              </h3>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-semibold text-gray-500">Submitted On</label>
                  <p class="text-lg font-medium">{{ formatDateTime(submission.created_at) }}</p>
                </div>
                <div>
                  <label class="text-sm font-semibold text-gray-500">Status</label>
                  <p>
                    <span :class="statusBadgeClass" class="text-sm font-semibold">
                      <i :class="statusIcon" class="mr-1"></i>
                      {{ statusText }}
                    </span>
                  </p>
                </div>
                <div>
                  <label class="text-sm font-semibold text-gray-500">Due Date</label>
                  <p class="text-lg" :class="dueDateClass">
                    {{ formattedDueDate }}
                    <span v-if="isLate" class="text-xs bg-red-500 text-white px-2 py-1 rounded ml-2">
                      Late
                    </span>
                  </p>
                </div>
                <div>
                  <label class="text-sm font-semibold text-gray-500">Current Grade</label>
                  <p v-if="submission.grade !== null" class="text-lg font-bold" :class="gradeColorClass">
                    {{ submission.grade }}/100
                  </p>
                  <p v-else class="text-gray-400">Not graded yet</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Submitted Content -->
          <div v-if="submission.submission_text" class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-border">
              <h3 class="text-xl font-semibold flex items-center gap-2">
                <i class="fas fa-file-alt text-green-500"></i>
                Student's Submission
              </h3>
            </div>
            <div class="p-6">
              <div class="prose dark:prose-invert max-w-none bg-gray-50 dark:bg-gray-800 rounded-xl p-6">
                <p class="whitespace-pre-wrap">{{ submission.submission_text }}</p>
              </div>
            </div>
          </div>

          <!-- Submitted File -->
          <div v-if="submission.file_url" class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-border">
              <h3 class="text-xl font-semibold flex items-center gap-2">
                <i class="fas fa-paperclip text-orange-500"></i>
                Attached File
              </h3>
            </div>
            <div class="p-6">
              <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <i class="fas fa-file text-green-500 text-2xl"></i>
                <div class="flex-1">
                  <p class="font-semibold">{{ getFileName(submission.file_url) }}</p>
                  <p class="text-sm text-gray-500">Submitted file</p>
                </div>
                <a :href="getDownloadRoute" 
                   class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center gap-2">
                  <i class="fas fa-download"></i>
                  Download
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Grading Panel -->
        <div class="space-y-6">
          <!-- Grading Form -->
          <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-border">
              <h3 class="text-xl font-semibold flex items-center gap-2">
                <i class="fas fa-graduation-cap text-purple-500"></i>
                Grade Assignment
              </h3>
            </div>

            <form @submit.prevent="gradeSubmission" class="p-6">
              <!-- Grade Input -->
              <div class="mb-6">
                <label class="block text-sm font-semibold mb-3 flex items-center gap-2">
                  <i class="fas fa-star text-yellow-500"></i>
                  Grade (0-100) *
                </label>
                <input
                  v-model="form.grade"
                  type="number"
                  min="0"
                  max="100"
                  step="0.5"
                  class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all"
                  placeholder="Enter grade..."
                  required
                >
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>Min: 0</span>
                  <span>Max: 100</span>
                </div>
              </div>

              <!-- Feedback -->
              <div class="mb-6">
                <label class="block text-sm font-semibold mb-3 flex items-center gap-2">
                  <i class="fas fa-comment-dots text-blue-500"></i>
                  Feedback
                </label>
                <textarea
                  v-model="form.feedback"
                  rows="6"
                  class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                  placeholder="Provide constructive feedback to the student..."
                  maxlength="2000"
                ></textarea>
                <div class="text-right text-sm text-gray-500 mt-1">
                  {{ form.feedback?.length || 0 }}/2000 characters
                </div>
              </div>

              <!-- Grade Actions -->
              <div class="space-y-3">
                <button
                  type="submit"
                  :disabled="isSubmitting"
                  :class="[
                    'w-full px-6 py-3 rounded-xl font-semibold transition-all flex items-center justify-center gap-2',
                    isSubmitting
                      ? 'bg-gray-400 text-gray-200 cursor-not-allowed'
                      : 'bg-gradient-to-r from-purple-500 to-pink-600 text-white hover:shadow-xl transform hover:scale-105'
                  ]"
                >
                  <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
                  <i v-else class="fas fa-check-circle"></i>
                  {{ isSubmitting ? 'Grading...' : (submission.grade !== null ? 'Update Grade' : 'Submit Grade') }}
                </button>

                <button
                  type="button"
                  @click="resetForm"
                  class="w-full px-6 py-3 border-2 border-border text-text rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all font-semibold flex items-center justify-center gap-2"
                >
                  <i class="fas fa-redo"></i>
                  Reset Form
                </button>
              </div>
            </form>
          </div>

          <!-- Grade Preview -->
          <div v-if="form.grade" class="bg-card border border-border rounded-2xl p-6 shadow-lg">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
              <i class="fas fa-chart-bar text-green-500"></i>
              Grade Preview
            </h3>
            <div class="text-center">
              <div class="text-4xl font-bold mb-2" :class="previewGradeColor">
                {{ form.grade }}
              </div>
              <div class="text-sm text-gray-500">out of 100</div>
              <div class="mt-3 text-sm" :class="previewGradeColor">
                {{ gradeRemarks }}
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
  submissionsRoute: String,
  gradeRoute: String,
  csrfToken: String
})

const toast = useToast()
const isSubmitting = ref(false)

const form = reactive({
  grade: props.submission.grade || '',
  feedback: props.submission.feedback || ''
})

// Computed properties
const formattedDueDate = computed(() => {
  if (!props.assignment.due_date) return 'No due date'
  return new Date(props.assignment.due_date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
})

const dueDateClass = computed(() => {
  if (!props.assignment.due_date) return 'text-gray-500'
  return isLate.value ? 'text-red-500' : 'text-green-500'
})

const isLate = computed(() => {
  if (!props.assignment.due_date) return false
  return new Date(props.submission.created_at) > new Date(props.assignment.due_date)
})

const statusText = computed(() => {
  return props.submission.grade !== null ? 'Graded' : 'Pending'
})

const statusBadgeClass = computed(() => {
  return props.submission.grade !== null 
    ? 'px-3 py-1 bg-green-100 text-green-600 rounded-full' 
    : 'px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full'
})

const statusIcon = computed(() => {
  return props.submission.grade !== null ? 'fas fa-check-circle' : 'fas fa-clock'
})

const gradeColorClass = computed(() => {
  const grade = props.submission.grade
  if (grade >= 90) return 'text-green-500'
  if (grade >= 80) return 'text-blue-500'
  if (grade >= 70) return 'text-yellow-500'
  if (grade >= 60) return 'text-orange-500'
  return 'text-red-500'
})

const previewGradeColor = computed(() => {
  const grade = parseFloat(form.grade) || 0
  if (grade >= 90) return 'text-green-500'
  if (grade >= 80) return 'text-blue-500'
  if (grade >= 70) return 'text-yellow-500'
  if (grade >= 60) return 'text-orange-500'
  return 'text-red-500'
})

const gradeRemarks = computed(() => {
  const grade = parseFloat(form.grade) || 0
  if (grade >= 90) return 'Excellent work! 🎉'
  if (grade >= 80) return 'Very good! 👍'
  if (grade >= 70) return 'Good job! 👏'
  if (grade >= 60) return 'Satisfactory ✅'
  return 'Needs improvement 📝'
})

const getDownloadRoute = computed(() => {
  return `/instructor/courses/${props.course.id}/assignments/${props.assignment.id}/submissions/${props.submission.id}/download`
})

// Methods
const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getFileName = (fileUrl) => {
  return fileUrl.split('/').pop() || 'Download File'
}

const gradeSubmission = async () => {
  if (!form.grade || form.grade < 0 || form.grade > 100) {
    toast.error('Please enter a valid grade between 0 and 100.')
    return
  }

  isSubmitting.value = true

  try {
    const response = await axios.post(props.gradeRoute, form, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })

    if (response.data.success) {
      toast.success('Submission graded successfully!')
      // Redirect back to submissions list
      setTimeout(() => {
        window.location.href = props.submissionsRoute
      }, 1500)
    }
  } catch (err) {
    console.error('Grade error:', err)
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors
      Object.values(errors).forEach(errorMessages => {
        errorMessages.forEach(message => toast.error(message))
      })
    } else {
      toast.error('Failed to grade submission. Please try again.')
    }
  } finally {
    isSubmitting.value = false
  }
}

const resetForm = () => {
  form.grade = props.submission.grade || ''
  form.feedback = props.submission.feedback || ''
  toast.info('Form reset to original values.')
}
</script>
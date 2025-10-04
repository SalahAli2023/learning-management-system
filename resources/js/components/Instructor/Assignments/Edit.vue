<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 py-8">
      
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="showRoute" class="text-text hover:text-blue-500 flex items-center gap-1">
          <i class="fas fa-arrow-left"></i>
          Back to Assignment
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-blue-500 font-medium">Edit Assignment</span>
      </nav>

      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-orange-500 to-red-600 bg-clip-text text-transparent">
            ✏️ Edit Assignment
          </h2>
          <p class="text-sm opacity-75 mt-1">Course: <strong>{{ course.title }}</strong></p>
        </div>
        
        <!-- Assignment Status -->
        <div class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
          <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
            <i class="fas fa-tasks text-white"></i>
          </div>
          <div>
            <h3 class="font-semibold text-blue-800 dark:text-blue-300">{{ assignment.title }}</h3>
            <p class="text-blue-700 dark:text-blue-400 text-sm">
              {{ assignment.submissions_count }} submissions
            </p>
          </div>
        </div>
      </div>

      <!-- Edit Form -->
      <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-border">
          <h3 class="text-xl font-semibold flex items-center gap-2">
            <i class="fas fa-edit text-orange-500"></i>
            Edit Assignment Details
          </h3>
        </div>

        <form @submit.prevent="updateAssignment" class="p-6">
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
            >
            <div class="flex items-center gap-4 mt-3">
              <button
                type="button"
                @click="clearDueDate"
                class="px-4 py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all text-sm flex items-center gap-2"
              >
                <i class="fas fa-times"></i>
                Clear Due Date
              </button>
              
              <button
                type="button"
                @click="extendDueDate"
                class="px-4 py-2 border border-green-500 text-green-500 rounded-lg hover:bg-green-500 hover:text-white transition-all text-sm flex items-center gap-2"
              >
                <i class="fas fa-plus"></i>
                Extend 1 Week
              </button>
            </div>
          </div>

          <!-- Submission Stats -->
          <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
            <h4 class="font-semibold mb-3 flex items-center gap-2">
              <i class="fas fa-chart-bar text-purple-500"></i>
              Submission Statistics
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-500">{{ assignment.submissions_count }}</div>
                <div class="text-xs text-gray-500">Total</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-green-500">{{ assignment.graded_submissions_count }}</div>
                <div class="text-xs text-gray-500">Graded</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-orange-500">{{ pendingSubmissions }}</div>
                <div class="text-xs text-gray-500">Pending</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-purple-500">{{ completionRate }}%</div>
                <div class="text-xs text-gray-500">Completion</div>
              </div>
            </div>
          </div>

          <!-- Warning Message if submissions exist -->
          <div v-if="hasSubmissions" class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 rounded-xl">
            <div class="flex items-start gap-3">
              <i class="fas fa-exclamation-triangle text-yellow-500 text-lg mt-0.5"></i>
              <div>
                <h4 class="font-semibold text-yellow-800 dark:text-yellow-300">Heads up!</h4>
                <p class="text-yellow-700 dark:text-yellow-400 text-sm">
                  This assignment has {{ assignment.submissions_count }} submissions. 
                  Changing the due date may affect late submission calculations.
                </p>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-between items-center pt-6 border-t border-border">
            <button
              type="button"
              @click="deleteAssignment"
              class="px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all font-semibold flex items-center gap-2"
            >
              <i class="fas fa-trash"></i>
              Delete Assignment
            </button>
            
            <div class="flex gap-3">
              <a :href="showRoute" 
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
                    : 'bg-gradient-to-r from-orange-500 to-red-600 text-white hover:shadow-xl transform hover:scale-105'
                ]"
              >
                <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-save"></i>
                {{ isSubmitting ? 'Updating...' : 'Update Assignment' }}
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Changes Preview -->
      <div v-if="hasChanges" class="mt-6 bg-card border border-border rounded-2xl p-6 shadow-lg">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
          <i class="fas fa-eye text-green-500"></i>
          Changes Preview
        </h3>
        <div class="space-y-3">
          <div v-if="form.title !== assignment.title" class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <i class="fas fa-heading text-blue-500"></i>
            <div>
              <span class="font-semibold">Title:</span>
              <span class="line-through text-gray-500 ml-2">{{ assignment.title }}</span>
              <span class="text-green-500 ml-2">→ {{ form.title }}</span>
            </div>
          </div>
          
          <div v-if="form.description !== assignment.description" class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
            <i class="fas fa-align-left text-green-500 mt-1"></i>
            <div>
              <span class="font-semibold">Description:</span>
              <div class="text-sm text-gray-500 mt-1 line-through">{{ assignment.description || 'Empty' }}</div>
              <div class="text-sm text-green-500 mt-1">{{ form.description || 'Empty' }}</div>
            </div>
          </div>
          
          <div v-if="form.due_date !== originalDueDate" class="flex items-center gap-3 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
            <i class="fas fa-calendar text-orange-500"></i>
            <div>
              <span class="font-semibold">Due Date:</span>
              <span class="line-through text-gray-500 ml-2">{{ formatDateTime(assignment.due_date) || 'No due date' }}</span>
              <span class="text-green-500 ml-2">→ {{ formatDateTime(form.due_date) || 'No due date' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import axios from 'axios'

const props = defineProps({
  course: Object,
  assignment: Object,
  updateRoute: String,
  showRoute: String,
  csrfToken: String
})

const toast = useToast()
const isSubmitting = ref(false)
const originalDueDate = ref('')

const form = reactive({
  title: '',
  description: '',
  due_date: ''
})

// Computed properties
const hasSubmissions = computed(() => {
  return props.assignment.submissions_count > 0
})

const pendingSubmissions = computed(() => {
  return props.assignment.submissions_count - props.assignment.graded_submissions_count
})

const completionRate = computed(() => {
  if (props.assignment.submissions_count === 0) return 0
  return Math.round((props.assignment.graded_submissions_count / props.assignment.submissions_count) * 100)
})

const hasChanges = computed(() => {
  return form.title !== props.assignment.title ||
         form.description !== props.assignment.description ||
         form.due_date !== originalDueDate.value
})

// Methods
const formatDateTime = (dateString) => {
  if (!dateString) return 'No due date'
  const date = new Date(dateString)
  return date.toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatForInput = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  date.setMinutes(date.getMinutes() - date.getTimezoneOffset())
  return date.toISOString().slice(0, 16)
}

const clearDueDate = () => {
  form.due_date = ''
  toast.info('Due date cleared.')
}

const extendDueDate = () => {
  if (!form.due_date) {
    // If no due date, set to 1 week from now
    const nextWeek = new Date()
    nextWeek.setDate(nextWeek.getDate() + 7)
    form.due_date = formatForInput(nextWeek.toISOString())
  } else {
    // Extend existing due date by 1 week
    const currentDueDate = new Date(form.due_date)
    currentDueDate.setDate(currentDueDate.getDate() + 7)
    form.due_date = formatForInput(currentDueDate.toISOString())
  }
  toast.success('Due date extended by 1 week.')
}

const updateAssignment = async () => {
  if (!form.title.trim()) {
    toast.error('Please enter an assignment title.')
    return
  }

  isSubmitting.value = true

  try {
    const response = await axios.put(props.updateRoute, form, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })

    if (response.data.success) {
      toast.success('Assignment updated successfully!')
      // Redirect to assignment show page
      setTimeout(() => {
        window.location.href = props.showRoute
      }, 1500)
    }
  } catch (err) {
    console.error('Update error:', err)
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors
      Object.values(errors).forEach(errorMessages => {
        errorMessages.forEach(message => toast.error(message))
      })
    } else {
      toast.error('Failed to update assignment. Please try again.')
    }
  } finally {
    isSubmitting.value = false
  }
}

const deleteAssignment = async () => {
  if (!confirm(`Delete assignment "${props.assignment.title}"? This will also delete all ${props.assignment.submissions_count} submissions. This action cannot be undone.`)) return

  try {
    const response = await axios.delete(props.updateRoute, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })

    if (response.data.success) {
      toast.success('Assignment deleted successfully!')
      // Redirect to assignments list
      setTimeout(() => {
        window.location.href = `/instructor/courses/${props.course.id}/assignments`
      }, 1500)
    }
  } catch (err) {
    console.error('Delete error:', err)
    toast.error('Failed to delete assignment.')
  }
}

// Initialize form with assignment data
onMounted(() => {
  form.title = props.assignment.title
  form.description = props.assignment.description || ''
  form.due_date = formatForInput(props.assignment.due_date)
  originalDueDate.value = form.due_date
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-through {
  text-decoration: line-through;
}
</style>
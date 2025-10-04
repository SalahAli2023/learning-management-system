<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 py-8">
      
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="assignmentsRoute" class="text-text hover:text-blue-500 flex items-center gap-1">
          <i class="fas fa-arrow-left"></i>
          Back to Assignments
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-blue-500 font-medium">Submissions</span>
      </nav>

      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-500 to-pink-600 bg-clip-text text-transparent">
            <span class="text-amber-50">📨</span> Submissions
          </h2>
          <p class="text-sm opacity-75 mt-1">
            <strong>{{ assignment.title }}</strong> - {{ course.title }}
          </p>
        </div>
        
        <!-- Due Date -->
        <div class="flex items-center gap-2" :class="dueDateClass">
          <i class="fas fa-calendar"></i>
          <span class="font-semibold">Due: {{ formattedDueDate }}</span>
          <span v-if="isOverdue" class="text-xs bg-red-500 text-white px-2 py-1 rounded">
            Overdue
          </span>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-paper-plane text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.total }}</h3>
          <p class="text-sm opacity-75">Total Submissions</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-check-circle text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.graded }}</h3>
          <p class="text-sm opacity-75">Graded</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-clock text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.pending }}</h3>
          <p class="text-sm opacity-75">Pending</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-chart-line text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ completionRate }}%</h3>
          <p class="text-sm opacity-75">Completion Rate</p>
        </div>
      </div>

      <!-- Submissions Table -->
      <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <h3 class="text-lg font-semibold flex items-center gap-2">
            <i class="fas fa-list-check text-purple-500"></i>
            Student Submissions ({{ submissions.data.length }})
          </h3>
          
          <!-- Filters -->
          <div class="flex gap-3">
            <select v-model="filterStatus" 
                    class="px-4 py-2 border-2 border-border rounded-xl bg-bg text-text focus:border-purple-500 transition-all">
              <option value="all">All Submissions</option>
              <option value="graded">Graded</option>
              <option value="pending">Pending</option>
              <option value="late">Late Submissions</option>
            </select>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-border">
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Student</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Submitted</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Grade</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr v-for="submission in filteredSubmissions" :key="submission.id" 
                  class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                      {{ submission.student.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <div class="font-medium text-text">{{ submission.student.name }}</div>
                      <div class="text-xs text-gray-500">{{ submission.student.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-col">
                    <span class="text-sm font-medium">{{ formatDate(submission.created_at) }}</span>
                    <span class="text-xs text-gray-500">{{ formatTime(submission.created_at) }}</span>
                    <span v-if="isLate(submission)" class="text-xs text-red-500 font-semibold mt-1">
                      <i class="fas fa-clock mr-1"></i>Late
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span :class="statusBadgeClass(submission)" class="text-xs font-semibold">
                    <i :class="statusIcon(submission)" class="mr-1"></i>
                    {{ statusText(submission) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div v-if="submission.grade !== null" class="flex items-center gap-2">
                    <span class="text-lg font-bold" :class="gradeColorClass(submission)">
                      {{ submission.grade }}
                    </span>
                    <span class="text-xs text-gray-500">/ 100</span>
                  </div>
                  <span v-else class="text-gray-400 text-sm">Not graded</span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex gap-2">
                    <a :href="getViewRoute(submission)" 
                       class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-300 transform hover:scale-110 tooltip"
                       title="View & Grade">
                      <i class="fas fa-eye"></i>
                    </a>
                    
                    <a v-if="submission.file_url" 
                       :href="getDownloadRoute(submission)"
                       class="p-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-300 transform hover:scale-110 tooltip"
                       title="Download File">
                      <i class="fas fa-download"></i>
                    </a>
                    
                    <button @click="deleteSubmission(submission)" 
                            class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-300 transform hover:scale-110 tooltip"
                            title="Delete Submission">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="filteredSubmissions.length === 0" class="text-center py-12 text-gray-500">
          <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
          <p class="text-lg mb-2">No submissions yet</p>
          <p class="text-sm opacity-75">Students haven't submitted this assignment yet.</p>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-6">
        <Pagination :links="submissions.links" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'
import axios from 'axios'
import Pagination from '../../UI/Pagination.vue'

const props = defineProps({
  course: Object,
  assignment: Object,
  submissions: Object,
  stats: Object,
  assignmentsRoute: String,
  csrfToken: String
})

const toast = useToast()
const filterStatus = ref('all')

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

const completionRate = computed(() => {
  if (props.stats.total === 0) return 0
  return Math.round((props.stats.graded / props.stats.total) * 100)
})

const filteredSubmissions = computed(() => {
  let filtered = [...props.submissions.data]
  
  switch (filterStatus.value) {
    case 'graded':
      filtered = filtered.filter(s => s.grade !== null)
      break
    case 'pending':
      filtered = filtered.filter(s => s.grade === null)
      break
    case 'late':
      filtered = filtered.filter(s => isLate(s))
      break
  }
  
  return filtered
})

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const isLate = (submission) => {
  if (!props.assignment.due_date) return false
  return new Date(submission.created_at) > new Date(props.assignment.due_date)
}

const statusText = (submission) => {
  if (submission.grade !== null) return 'Graded'
  return isLate(submission) ? 'Late' : 'Submitted'
}

const statusBadgeClass = (submission) => {
  if (submission.grade !== null) return 'px-3 py-1 bg-green-100 text-green-600 rounded-full'
  return isLate(submission) 
    ? 'px-3 py-1 bg-red-100 text-red-600 rounded-full' 
    : 'px-3 py-1 bg-blue-100 text-blue-600 rounded-full'
}

const statusIcon = (submission) => {
  if (submission.grade !== null) return 'fas fa-check-circle'
  return isLate(submission) ? 'fas fa-exclamation-triangle' : 'fas fa-paper-plane'
}

const gradeColorClass = (submission) => {
  const grade = submission.grade
  if (grade >= 90) return 'text-green-500'
  if (grade >= 80) return 'text-blue-500'
  if (grade >= 70) return 'text-yellow-500'
  if (grade >= 60) return 'text-orange-500'
  return 'text-red-500'
}

const getViewRoute = (submission) => {
  return `/instructor/courses/${props.course.id}/assignments/${props.assignment.id}/submissions/${submission.id}`
}

const getDownloadRoute = (submission) => {
  return `/instructor/courses/${props.course.id}/assignments/${props.assignment.id}/submissions/${submission.id}/download`
}

const deleteSubmission = async (submission) => {
  if (!confirm(`Delete submission from ${submission.student.name}?`)) return
  
  try {
    const response = await axios.delete(
      `/instructor/courses/${props.course.id}/assignments/${props.assignment.id}/submissions/${submission.id}`,
      { headers: { 'X-CSRF-TOKEN': props.csrfToken } }
    )
    
    if (response.data.success) {
      toast.success('Submission deleted successfully!')
      window.location.reload()
    }
  } catch (err) {
    console.error('Delete error:', err)
    toast.error('Failed to delete submission.')
  }
}
</script>

<style scoped>
.tooltip {
  position: relative;
}

.tooltip:hover::after {
  content: attr(title);
  position: absolute;
  bottom: -30px;
  left: 50%;
  transform: translateX(-50%);
  background: #333;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  z-index: 1000;
}

.tooltip:hover::before {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  border: 5px solid transparent;
  border-bottom-color: #333;
}
</style>
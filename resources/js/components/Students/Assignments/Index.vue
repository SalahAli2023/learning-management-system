<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-6xl mx-auto px-4 py-8">
      
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="courseRoute" class="text-text hover:text-blue-500 flex items-center gap-1">
          <i class="fas fa-arrow-left"></i>
          Back to Course
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-blue-500 font-medium">Assignments</span>
      </nav>

      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-green-500 to-blue-600 bg-clip-text text-transparent">
            <span class="text-amber-50">📝</span> Assignments
          </h2>
          <p class="text-sm opacity-75 mt-1">Course: <strong>{{ course.title }}</strong></p>
        </div>
        
        <!-- Stats -->
        <div class="flex gap-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-blue-500">{{ stats.total }}</div>
            <div class="text-xs text-gray-500">Total</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-500">{{ stats.submitted }}</div>
            <div class="text-xs text-gray-500">Submitted</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-purple-500">{{ stats.graded }}</div>
            <div class="text-xs text-gray-500">Graded</div>
          </div>
        </div>
      </div>

      <!-- Assignments List -->
      <div class="space-y-6">
        <div v-for="assignment in assignments" :key="assignment.id" 
             class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
          
          <!-- Assignment Header -->
          <div class="p-6 border-b border-border">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
              <div class="flex-1">
                <h3 class="text-xl font-bold mb-2">{{ assignment.title }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                  {{ assignment.description }}
                </p>
              </div>
              
              <div class="flex flex-col items-end gap-2">
                <!-- Due Date -->
                <div class="flex items-center gap-2" :class="getDueDateClass(assignment)">
                  <i class="fas fa-calendar"></i>
                  <span class="text-sm font-semibold">
                    Due: {{ formatDate(assignment.due_date) }}
                  </span>
                  <span v-if="isOverdue(assignment)" class="text-xs bg-red-500 text-white px-2 py-1 rounded">
                    Overdue
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Submission Status -->
          <div class="p-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
              <div class="flex-1">
                <!-- Submission Info -->
                <div v-if="assignment.submissions && assignment.submissions.length > 0" class="space-y-2">
                  <div class="flex items-center gap-3">
                    <span :class="getStatusBadgeClass(assignment.submissions[0])" class="text-sm font-semibold">
                      <i :class="getStatusIcon(assignment.submissions[0])" class="mr-1"></i>
                      {{ getStatusText(assignment.submissions[0]) }}
                    </span>
                    
                    <span class="text-sm text-gray-500">
                      Submitted: {{ formatDateTime(assignment.submissions[0].created_at) }}
                    </span>

                    <span v-if="assignment.submissions[0].grade !== null" 
                          class="text-sm font-bold text-green-500">
                      Grade: {{ assignment.submissions[0].grade }}/100
                    </span>
                  </div>

                  <!-- Feedback Preview -->
                  <div v-if="assignment.submissions[0].feedback" 
                       class="text-sm text-gray-600 bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                    <strong>Instructor Feedback:</strong> 
                    {{ truncateText(assignment.submissions[0].feedback, 100) }}
                  </div>
                </div>

                <!-- No Submission -->
                <div v-else class="text-orange-500 text-sm flex items-center gap-2">
                  <i class="fas fa-exclamation-circle"></i>
                  Not submitted yet
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-2">
                <!-- View Assignment Button -->
                <a :href="getAssignmentRoute(assignment)" 
                   class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center gap-2">
                  <i class="fas fa-eye"></i>
                  View Details
                </a>

                <!-- Submit/Resubmit Button -->
                <button v-if="!hasSubmission(assignment)"
                        @click="goToSubmit(assignment)"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition flex items-center gap-2 font-semibold">
                  <i class="fas fa-paper-plane"></i>
                  Submit Assignment
                </button>

                <!-- View Submission Button -->
                <a v-if="hasSubmission(assignment)" 
                   :href="getSubmissionRoute(assignment)"
                   class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition flex items-center gap-2">
                  <i class="fas fa-file-alt"></i>
                  View Submission
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="assignments.length === 0" class="text-center py-12">
        <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-tasks text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-bold text-text mb-2">No assignments yet</h3>
        <p class="text-gray-500">Your instructor hasn't created any assignments for this course.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  course: Object,
  assignments: Array,
  courseRoute: String,
  csrfToken: String
})

// Computed properties
const stats = computed(() => {
  const total = props.assignments.length
  const submitted = props.assignments.filter(a => a.submissions && a.submissions.length > 0).length
  const graded = props.assignments.filter(a => 
    a.submissions && a.submissions.length > 0 && a.submissions[0].grade !== null
  ).length
  
  return { total, submitted, graded }
})

// Methods
const formatDate = (dateString) => {
  if (!dateString) return 'No due date'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

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

const getDueDateClass = (assignment) => {
  if (!assignment.due_date) return 'text-gray-500'
  
  const dueDate = new Date(assignment.due_date)
  const now = new Date()
  
  if (dueDate < now) return 'text-red-500'
  if (dueDate < new Date(now.getTime() + 24 * 60 * 60 * 1000)) return 'text-orange-500'
  return 'text-green-500'
}

const isOverdue = (assignment) => {
  if (!assignment.due_date) return false
  return new Date(assignment.due_date) < new Date()
}

const hasSubmission = (assignment) => {
  return assignment.submissions && assignment.submissions.length > 0
}

const getStatusText = (submission) => {
  if (submission.grade !== null) return 'Graded'
  return 'Submitted'
}

const getStatusBadgeClass = (submission) => {
  if (submission.grade !== null) return 'px-3 py-1 bg-green-100 text-green-600 rounded-full'
  return 'px-3 py-1 bg-blue-100 text-blue-600 rounded-full'
}

const getStatusIcon = (submission) => {
  if (submission.grade !== null) return 'fas fa-check-circle'
  return 'fas fa-paper-plane'
}

const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

const getAssignmentRoute = (assignment) => {
  return `/student/courses/${props.course.id}/assignments/${assignment.id}`
}

const getSubmissionRoute = (assignment) => {
  if (!hasSubmission(assignment)) return '#'
  return `/student/courses/${props.course.id}/assignments/${assignment.id}/submissions/${assignment.submissions[0].id}`
}

const goToSubmit = (assignment) => {
  window.location.href = getAssignmentRoute(assignment)
}
</script>
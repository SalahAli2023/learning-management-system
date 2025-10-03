<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 py-8">
      
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
          <p class="text-sm opacity-75 mt-1">Manage assignments for: <strong>{{ course.title }}</strong></p>
        </div>
        
        <a :href="createRoute"
          class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-blue-600 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 font-semibold">
          <i class="fas fa-plus"></i>
          New Assignment
        </a>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-tasks text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.total }}</h3>
          <p class="text-sm opacity-75">Total Assignments</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-paper-plane text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.submissions }}</h3>
          <p class="text-sm opacity-75">Total Submissions</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mx-auto mb-3">
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
      </div>

      <!-- Assignments Table -->
      <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-border">
          <h3 class="text-lg font-semibold flex items-center gap-2">
            <i class="fas fa-list text-green-500"></i>
            Assignments List ({{ assignments.data.length }})
          </h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-border">
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Title</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Due Date</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Submissions</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr v-for="assignment in assignments.data" :key="assignment.id" 
                  class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                <td class="px-6 py-4">
                  <div class="font-medium text-text">{{ assignment.title }}</div>
                  <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ assignment.description }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2" :class="dueDateClass(assignment)">
                    <i class="fas fa-calendar"></i>
                    <span class="text-sm">{{ formatDate(assignment.due_date) }}</span>
                    <span v-if="isOverdue(assignment)" class="text-xs bg-red-500 text-white px-2 py-1 rounded">
                      Overdue
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="text-center">
                      <div class="text-lg font-bold text-blue-500">{{ assignment.submissions_count }}</div>
                      <div class="text-xs text-gray-500">Total</div>
                    </div>
                    <div class="text-center">
                      <div class="text-lg font-bold text-green-500">{{ assignment.graded_submissions_count }}</div>
                      <div class="text-xs text-gray-500">Graded</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span :class="statusBadgeClass(assignment)" class="text-xs font-semibold">
                    <i :class="statusIcon(assignment)" class="mr-1"></i>
                    {{ statusText(assignment) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex gap-2">
                    <a :href="getViewRoute(assignment)" 
                       class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-300 transform hover:scale-110 tooltip"
                       title="View Assignment">
                      <i class="fas fa-eye"></i>
                    </a>
                    
                    <a :href="getEditRoute(assignment)" 
                       class="p-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-300 transform hover:scale-110 tooltip"
                       title="Edit Assignment">
                      <i class="fas fa-edit"></i>
                    </a>
                    
                    <a :href="getSubmissionsRoute(assignment)" 
                       class="p-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-all duration-300 transform hover:scale-110 tooltip"
                       title="View Submissions">
                      <i class="fas fa-list-check"></i>
                    </a>
                    
                    <button @click="deleteAssignment(assignment)" 
                            class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-300 transform hover:scale-110 tooltip"
                            title="Delete Assignment">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="assignments.data.length === 0" class="text-center py-12 text-gray-500">
          <i class="fas fa-tasks text-4xl mb-3 opacity-50"></i>
          <p class="text-lg mb-2">No assignments yet</p>
          <p class="text-sm opacity-75 mb-4">Create your first assignment to get started</p>
          <a :href="createRoute"
             class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-blue-600 text-white rounded-xl shadow-lg hover:shadow-xl transition">
            <i class="fas fa-plus"></i>
            Create First Assignment
          </a>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-6">
        <Pagination :links="assignments.links" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useToast } from 'vue-toastification'
import axios from 'axios'
import Pagination from '../../UI/Pagination.vue'

const props = defineProps({
  course: Object,
  assignments: Object,
  createRoute: String,
  courseRoute: String,
  csrfToken: String
})

const toast = useToast()

// Computed properties
const stats = computed(() => {
  const total = props.assignments.data.length
  const submissions = props.assignments.data.reduce((sum, assignment) => sum + assignment.submissions_count, 0)
  const graded = props.assignments.data.reduce((sum, assignment) => sum + assignment.graded_submissions_count, 0)
  const pending = submissions - graded
  
  return { total, submissions, graded, pending }
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

const dueDateClass = (assignment) => {
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

const statusText = (assignment) => {
  if (!assignment.due_date) return 'No Due Date'
  return isOverdue(assignment) ? 'Overdue' : 'Active'
}

const statusBadgeClass = (assignment) => {
  if (!assignment.due_date) return 'px-3 py-1 bg-gray-100 text-gray-600 rounded-full'
  return isOverdue(assignment) 
    ? 'px-3 py-1 bg-red-100 text-red-600 rounded-full' 
    : 'px-3 py-1 bg-green-100 text-green-600 rounded-full'
}

const statusIcon = (assignment) => {
  if (!assignment.due_date) return 'fas fa-infinity'
  return isOverdue(assignment) ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle'
}

const getViewRoute = (assignment) => {
  return `/instructor/courses/${props.course.id}/assignments/${assignment.id}`
}

const getEditRoute = (assignment) => {
  return `/instructor/courses/${props.course.id}/assignments/${assignment.id}/edit`
}

const getSubmissionsRoute = (assignment) => {
  return `/instructor/courses/${props.course.id}/assignments/${assignment.id}/submissions`
}

const deleteAssignment = async (assignment) => {
  if (!confirm(`Delete assignment "${assignment.title}"? This will also delete all submissions.`)) return
  
  try {
    const response = await axios.delete(`/instructor/courses/${props.course.id}/assignments/${assignment.id}`, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })
    
    if (response.data.success) {
      toast.success(`Assignment "${assignment.title}" deleted successfully!`)
      // Reload page to reflect changes
      window.location.reload()
    }
  } catch (err) {
    console.error('Delete error:', err)
    toast.error('Failed to delete assignment.')
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

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
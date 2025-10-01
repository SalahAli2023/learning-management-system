<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-500 to-pink-600 bg-clip-text text-transparent">
            👥 Course Enrollments
          </h2>
          <p class="text-sm opacity-75 mt-1">Manage students enrolled in: <strong>{{ course.title }}</strong></p>
        </div>
        <div class="flex gap-3">
          <a :href="courseRoute"
            class="flex items-center gap-2 px-6 py-3 border-2 border-border text-text rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all duration-300 font-semibold">
            <i class="fas fa-arrow-left"></i>
            Back to Course
          </a>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-users text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.total }}</h3>
          <p class="text-sm opacity-75">Total Students</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-play-circle text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.active }}</h3>
          <p class="text-sm opacity-75">Active</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-check-circle text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.completed }}</h3>
          <p class="text-sm opacity-75">Completed</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-gray-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-clock text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.pending }}</h3>
          <p class="text-sm opacity-75">Pending</p>
        </div>
      </div>

      <!-- Enrollments Table -->
      <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <h3 class="text-lg font-semibold flex items-center gap-2">
            <i class="fas fa-list text-purple-500"></i>
            Enrolled Students ({{ enrollments.data.length }})
          </h3>
          
          <!-- Filters -->
          <div class="flex gap-3">
            <select v-model="filterStatus" 
                    @change="filterEnrollments"
                    class="px-4 py-2 border-2 border-border rounded-xl bg-bg text-text focus:border-purple-500 transition-all">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="completed">Completed</option>
              <option value="pending">Pending</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-border">
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Student</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Enrolled Date</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Progress</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr v-for="enrollment in filteredEnrollments" :key="enrollment.id" 
                  class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                      {{ enrollment.student.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <div class="font-medium text-text">{{ enrollment.student.name }}</div>
                      <div class="text-xs text-gray-500">{{ enrollment.student.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  {{ formatDate(enrollment.enrolled_at) }}
                </td>
                <td class="px-6 py-4">
                  <span :class="getStatusBadgeClass(enrollment.status)">
                    <i :class="getStatusIcon(enrollment.status)" class="mr-1"></i>
                    {{ enrollment.status }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                      <div class="bg-green-500 h-2 rounded-full" :style="{ width: getProgress(enrollment) + '%' }"></div>
                    </div>
                    <span class="text-xs font-medium">{{ getProgress(enrollment) }}%</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <button @click="updateStatus(enrollment, 'active')" 
                            v-if="enrollment.status !== 'active'"
                            class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-xs">
                      Activate
                    </button>
                    <button @click="updateStatus(enrollment, 'completed')" 
                            v-if="enrollment.status === 'active'"
                            class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-xs">
                      Complete
                    </button>
                    <button @click="updateStatus(enrollment, 'cancelled')" 
                            v-if="enrollment.status !== 'cancelled'"
                            class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs">
                      Cancel
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="filteredEnrollments.length === 0" class="text-center py-12 text-gray-500">
          <i class="fas fa-users text-4xl mb-3 opacity-50"></i>
          <p class="text-lg mb-2">No enrollments found</p>
          <p class="text-sm opacity-75">No students have enrolled in this course yet.</p>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-6">
        <Pagination :links="enrollments.links" />
      </div>
    </div>
  </div>
</template>

<script>
import Pagination from '../UI/Pagination.vue'

export default {
  components: {
    Pagination
  },
  
  props: {
    enrollments: Object,
    course: Object,
    courseRoute: String,
    csrfToken: String
  },

  data() {
    return {
      localEnrollments: [...this.enrollments.data],
      filterStatus: 'all'
    }
  },

  computed: {
    stats() {
      const total = this.localEnrollments.length
      const active = this.localEnrollments.filter(e => e.status === 'active').length
      const completed = this.localEnrollments.filter(e => e.status === 'completed').length
      const pending = this.localEnrollments.filter(e => e.status === 'pending').length
      
      return { total, active, completed, pending }
    },

    filteredEnrollments() {
      if (this.filterStatus === 'all') {
        return this.localEnrollments
      }
      return this.localEnrollments.filter(e => e.status === this.filterStatus)
    }
  },

  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString()
    },

    getStatusBadgeClass(status) {
      const classes = {
        active: 'px-3 py-1 bg-green-100 text-green-600 border border-green-200 rounded-full text-xs font-semibold',
        completed: 'px-3 py-1 bg-purple-100 text-purple-600 border border-purple-200 rounded-full text-xs font-semibold',
        pending: 'px-3 py-1 bg-yellow-100 text-yellow-600 border border-yellow-200 rounded-full text-xs font-semibold',
        cancelled: 'px-3 py-1 bg-red-100 text-red-600 border border-red-200 rounded-full text-xs font-semibold'
      }
      return classes[status] || classes.pending
    },

    getStatusIcon(status) {
      const icons = {
        active: 'fas fa-play-circle',
        completed: 'fas fa-check-circle',
        pending: 'fas fa-clock',
        cancelled: 'fas fa-times-circle'
      }
      return icons[status] || 'fas fa-clock'
    },

    getProgress(enrollment) {
      const statusProgress = {
        pending: 10,
        active: 50,
        completed: 100,
        cancelled: 0
      }
      return statusProgress[enrollment.status] || 0
    },

    async updateStatus(enrollment, newStatus) {
      try {
        const response = await this.$axios.put(
          `/instructor/courses/${this.course.id}/enrollments/${enrollment.id}`,
          { status: newStatus },
          { headers: { 'X-CSRF-TOKEN': this.csrfToken } }
        )

        if (response.data.success) {
          this.$toast.success(`Enrollment status updated to ${newStatus}!`)
          const index = this.localEnrollments.findIndex(e => e.id === enrollment.id)
          if (index !== -1) {
            this.localEnrollments[index].status = newStatus
            if (newStatus === 'completed') {
              this.localEnrollments[index].completed_at = new Date().toISOString()
            }
          }
        }
      } catch (err) {
        console.error('Update error:', err)
        this.$toast.error('Failed to update enrollment status.')
      }
    },

    filterEnrollments() {
      // Filtering is handled by computed property
    }
  },

  mounted() {
    console.log('Enrollments loaded:', this.localEnrollments.length)
  }
}
</script>
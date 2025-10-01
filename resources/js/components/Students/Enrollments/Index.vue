<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-green-500 to-blue-600 bg-clip-text text-transparent">
            <span class="text-amber-50">📚</span>  My Courses
          </h2>
          <p class="text-sm opacity-75 mt-1">Courses you're enrolled in</p>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-book-open text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.total }}</h3>
          <p class="text-sm opacity-75">All Courses</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-play-circle text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.active }}</h3>
          <p class="text-sm opacity-75">In Progress</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-check-circle text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.completed }}</h3>
          <p class="text-sm opacity-75">Completed</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-clock text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ stats.pending }}</h3>
          <p class="text-sm opacity-75">Pending</p>
        </div>
      </div>

      <!-- Courses Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="enrollment in enrollments.data" :key="enrollment.id" 
            class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
          <div class="h-2 bg-gradient-to-r from-blue-500 to-purple-600"></div>
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-xl font-bold text-text line-clamp-2">{{ enrollment.course.title }}</h3>
              <span :class="getStatusBadgeClass(enrollment.status)" class="text-xs">
                {{ enrollment.status }}
              </span>
            </div>
            
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
              {{ enrollment.course.description }}
            </p>
            
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
              <i class="fas fa-user-tie text-blue-500"></i>
              <span>By {{ enrollment.course.instructor.name }}</span>
            </div>
            
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
              <i class="fas fa-film text-green-500"></i>
              <span>{{ enrollment.course.lessons?.length || 0 }} lessons</span>
            </div>
            
            <div class="flex justify-between items-center">
              <div class="flex gap-2">
                <a :href="getCourseRoute(enrollment)" 
                   class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm">
                  Enter Course
                </a>
                <button v-if="enrollment.status !== 'completed'" 
                        @click="withdrawCourse(enrollment)"
                        class="px-4 py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition text-sm">
                  Withdraw
                </button>
              </div>
              
              <div class="text-xs text-gray-500">
                Enrolled {{ formatDate(enrollment.enrolled_at) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="enrollments.data.length === 0" class="text-center py-12">
        <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-book-open text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-bold text-text mb-2">No courses yet</h3>
        <p class="text-gray-500 mb-6">You haven't enrolled in any courses yet.</p>
        <a href="/courses" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl shadow-lg hover:shadow-xl transition">
          <i class="fas fa-search"></i>
          Browse Courses
        </a>
      </div>

      <!-- Pagination -->
      <div class="mt-8">
        <Pagination :links="enrollments.links" />
      </div>
    </div>
  </div>
</template>

<script>
import Pagination from '../../UI/Pagination.vue'

export default {
  components: {
    Pagination
  },

  props: {
    enrollments: Object,
    csrfToken: String
  },

  computed: {
    stats() {
      const total = this.enrollments.data.length
      const active = this.enrollments.data.filter(e => e.status === 'active').length
      const completed = this.enrollments.data.filter(e => e.status === 'completed').length
      const pending = this.enrollments.data.filter(e => e.status === 'pending').length
      
      return { total, active, completed, pending }
    }
  },

  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString()
    },

    getStatusBadgeClass(status) {
      const classes = {
        active: 'px-2 py-1 bg-green-100 text-green-600 border border-green-200 rounded-full text-xs font-semibold',
        completed: 'px-2 py-1 bg-purple-100 text-purple-600 border border-purple-200 rounded-full text-xs font-semibold',
        pending: 'px-2 py-1 bg-yellow-100 text-yellow-600 border border-yellow-200 rounded-full text-xs font-semibold',
        cancelled: 'px-2 py-1 bg-red-100 text-red-600 border border-red-200 rounded-full text-xs font-semibold'
      }
      return classes[status] || classes.pending
    },

    getCourseRoute(enrollment) {
      return `/student/courses/${enrollment.course.id}`
    },

    async withdrawCourse(enrollment) {
      if (!confirm(`Are you sure you want to withdraw from "${enrollment.course.title}"?`)) return
      
      try {
        const response = await this.$axios.delete(`/student/enrollments/${enrollment.id}`, {
          headers: { 'X-CSRF-TOKEN': this.csrfToken }
        })

        if (response.data.success) {
          this.$toast.success('Successfully withdrawn from the course!')
          // Reload page to reflect changes
          window.location.reload()
        }
      } catch (err) {
        console.error('Withdraw error:', err)
        this.$toast.error('Failed to withdraw from course.')
      }
    }
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
</style>
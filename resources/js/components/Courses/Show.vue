<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 py-8">
      
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="indexRoute" class="text-text hover:text-blue-500 transition-colors flex items-center gap-1">
          <i class="fas fa-home"></i>
          Courses
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-blue-500 font-medium">{{ course.title }}</span>
      </nav>

      <!-- Course Header -->
      <div class="bg-card border border-border rounded-2xl shadow-xl overflow-hidden mb-8">
        <!-- Cover Image -->
        <div v-if="course.cover_url" class="h-64 bg-gradient-to-br from-blue-400 to-purple-600 relative overflow-hidden">
          <img 
            :src="course.cover_url" 
            :alt="course.title"
            class="w-full h-full object-cover"
          />
          <div class="absolute inset-0 bg-black/20"></div>
        </div>
        <div v-else class="h-64 bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center">
          <i class="fas fa-book-open text-white text-6xl opacity-80"></i>
        </div>

        <!-- Course Info -->
        <div class="p-8">
          <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <div class="flex-1">
              <!-- Status Badge -->
              <span 
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold shadow-lg mb-4"
                :class="course.is_published 
                  ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-300 dark:border-green-700' 
                  : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-700'"
              >
                <i :class="course.is_published ? 'fas fa-globe' : 'fas fa-lock'"></i>
                {{ course.is_published ? 'Published' : 'Draft' }}
              </span>

              <!-- Title -->
              <h1 class="text-4xl font-bold mb-3 bg-gradient-to-r from-gray-800 to-gray-600 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent">
                {{ course.title }}
              </h1>

              <!-- Category -->
              <div class="flex items-center gap-3 mb-4">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                  <i class="fas fa-tag"></i>
                  {{ course.category }}
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                  <i class="fas fa-calendar mr-1"></i>
                  {{ formatDate(course.created_at) }}
                </span>
              </div>

              <!-- Description -->
              <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                {{ course.description || 'No description provided for this course.' }}
              </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row lg:flex-col gap-3">
              <a 
                :href="editRoute" 
                class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 font-semibold"
              >
                <i class="fas fa-edit"></i>
                Edit Course
              </a>
              <a 
                :href="indexRoute" 
                class="flex items-center justify-center gap-2 px-6 py-3 bg-bg border-2 border-border text-text rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all duration-300 font-semibold"
              >
                <i class="fas fa-arrow-left"></i>
                Back to List
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Course Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow group">
          <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <i class="fas fa-calendar text-blue-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-blue-600">{{ formatDate(course.created_at) }}</div>
          <div class="text-sm text-gray-600 dark:text-gray-400">Created</div>
        </div>

        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow group">
          <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <i class="fas fa-sync-alt text-green-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-green-600">{{ formatDate(course.updated_at) }}</div>
          <div class="text-sm text-gray-600 dark:text-gray-400">Last Updated</div>
        </div>

        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow group">
          <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <i class="fas fa-users text-purple-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-purple-600">{{ course.enrollments.length || 0 }}</div>
          <div class="text-sm text-gray-600 dark:text-gray-400">Enrolled</div>
        </div>

        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow group">
          <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <i class="fas fa-chart-line text-orange-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-orange-600">{{ course.lessons.length|| 0 }}</div>
          <div class="text-sm text-gray-600 dark:text-gray-400">Lessons</div>
        </div>
      </div>

      <!-- Course Content Tabs -->
      <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <!-- Tabs Header -->
        <div class="border-b border-border">
          <nav class="flex overflow-x-auto">
            <button 
              v-for="tab in tabs" 
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'px-6 py-4 font-semibold text-sm border-b-2 transition-all duration-200 whitespace-nowrap',
                activeTab === tab.id 
                  ? 'border-blue-500 text-blue-600 bg-blue-50 dark:bg-blue-900/20' 
                  : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
              ]"
            >
              <i :class="tab.icon" class="mr-2"></i>
              {{ tab.name }}
            </button>
          </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
          <!-- Overview Tab -->
          <div v-if="activeTab === 'overview'" class="space-y-6">
            <div>
              <h3 class="text-lg font-semibold mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Course Overview
              </h3>
              <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                {{ course.description || 'No detailed description available for this course.' }}
              </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                <h4 class="font-semibold mb-2 flex items-center gap-2">
                  <i class="fas fa-tag text-green-500"></i>
                  Category
                </h4>
                <p>{{ course.category }}</p>
              </div>
              <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                <h4 class="font-semibold mb-2 flex items-center gap-2">
                  <i class="fas fa-user-tie text-purple-500"></i>
                  Instructor
                </h4>
                <p>{{ course.instructor?.name || 'Unknown Instructor' }}</p>
              </div>
            </div>
          </div>

          <!-- Lessons Tab -->
          <div v-if="activeTab === 'lessons'" class="space-y-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
              <i class="fas fa-play-circle text-green-500"></i>
              Course Lessons
            </h3>
            
            <div v-if="course.lessons && course.lessons.length > 0" class="space-y-3">
              <div 
                v-for="lesson in course.lessons" 
                :key="lesson.id"
                class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
              >
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                  <i class="fas fa-play text-blue-500"></i>
                </div>
                <div class="flex-1">
                  <h4 class="font-semibold">{{ lesson.title }}</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ lesson.duration }} min</p>
                </div>
                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded text-xs">
                  {{ lesson.is_free ? 'Free' : 'Premium' }}
                </span>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <i class="fas fa-film text-4xl mb-3 opacity-50"></i>
              <p>No lessons available yet.</p>
            </div>
          </div>

          <!-- Students Tab -->
          <div v-if="activeTab === 'students'" class="space-y-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
              <i class="fas fa-users text-purple-500"></i>
              Enrolled Students
            </h3>
            
            <div v-if="course.enrollments && course.enrollments.length > 0" class="space-y-3">
              <div 
                v-for="enrollment in course.enrollments" 
                :key="enrollment.id"
                class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl"
              >
                <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center text-white font-semibold">
                  {{ getInitials(enrollment.student?.name) }}
                </div>
                <div class="flex-1">
                  <h4 class="font-semibold">{{ enrollment.student?.name }}</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Enrolled {{ formatDate(enrollment.created_at) }}</p>
                </div>
                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded text-xs">
                  {{ enrollment.progress || 0 }}% Complete
                </span>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <i class="fas fa-user-graduate text-4xl mb-3 opacity-50"></i>
              <p>No students enrolled yet.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CourseShow',
  
  props: {
    course: {
      type: Object,
      required: true
    },
    indexRoute: {
      type: String,
      required: true
    },
    editRoute: {
      type: Function,
      required: true
    }
  },

  data() {
    return {
      activeTab: 'overview',
      tabs: [
        { id: 'overview', name: 'Overview', icon: 'fas fa-info-circle' },
        { id: 'lessons', name: 'Lessons', icon: 'fas fa-play-circle' },
        { id: 'students', name: 'Students', icon: 'fas fa-users' }
      ]
    }
  },

  methods: {
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    },

    getInitials(name) {
      if (!name) return '??'
      return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
    },
    // editRoute(course) {
    //   return `/instructor/courses/${course.id}/edit`;
    // },
  }
}
</script>

<!-- <style scoped>
/* Smooth transitions for tabs */
button {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effects */
.hover-lift:hover {
  transform: translateY(-2px);
}

/* Custom scrollbar for tabs */
nav::-webkit-scrollbar {
  height: 4px;
}

nav::-webkit-scrollbar-track {
  background: transparent;
}

nav::-webkit-scrollbar-thumb {
  background: var(--color-border);
  border-radius: 2px;
}

/* Loading animations */
.fa-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Gradient text animation */
.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  background-size: 200% 200%;
  animation: gradient 3s ease infinite;
}

@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style> -->
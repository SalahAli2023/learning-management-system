<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 py-8">
      
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="coursesIndexRoute" class="text-text hover:text-blue-500 flex items-center gap-1 transition-all">
          <i class="fas fa-arrow-left"></i>
          Back to Courses
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-blue-500 font-medium">{{ course.title }}</span>
      </nav>

      <!-- Course Header -->
      <div class="bg-card border border-border rounded-2xl shadow-xl overflow-hidden mb-8">
        <div class="p-8">
          <div class="flex flex-col lg:flex-row justify-between items-start gap-6">
            <div class="flex-1">
              <h1 class="text-4xl font-bold mb-4">{{ course.title }}</h1>
              <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed mb-6">
                {{ course.description }}
              </p>
              
              <!-- Course Badges -->
              <div class="flex flex-wrap gap-3 mb-4">
                <span class="px-4 py-2 bg-blue-500 text-white rounded-xl text-sm font-semibold flex items-center gap-2">
                  <i class="fas fa-user-tie"></i>
                  {{ course.instructor.name }}
                </span>
                <span class="px-4 py-2 bg-green-500 text-white rounded-xl text-sm font-semibold flex items-center gap-2">
                  <i class="fas fa-play-circle"></i>
                  {{ course.lessons.length }} Lessons
                </span>
                <span class="px-4 py-2 bg-purple-500 text-white rounded-xl text-sm font-semibold flex items-center gap-2">
                  <i class="fas fa-clock"></i>
                  {{ totalDuration }} total
                </span>
                <span v-if="course.is_free" class="px-4 py-2 bg-green-500 text-white rounded-xl text-sm font-semibold flex items-center gap-2">
                  <i class="fas fa-unlock"></i>
                  Free Course
                </span>
                <span v-else class="px-4 py-2 bg-orange-500 text-white rounded-xl text-sm font-semibold flex items-center gap-2">
                  <i class="fas fa-crown"></i>
                  Premium Course
                </span>
              </div>

              <!-- Enrollment Status Messages -->
              <div v-if="isEnrolled && enrollmentStatus === 'active'" 
                   class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200">
                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                  <i class="fas fa-check text-white text-lg"></i>
                </div>
                <div>
                  <h3 class="font-semibold text-green-800 dark:text-green-300">🎉 Successfully Enrolled!</h3>
                  <p class="text-green-700 dark:text-green-400 text-sm">
                    You have full access to all course content. Start your learning journey now!
                  </p>
                </div>
              </div>

              <div v-else-if="isEnrolled && enrollmentStatus === 'pending'" 
                   class="flex items-center gap-3 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-200">
                <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                  <i class="fas fa-clock text-white text-lg"></i>
                </div>
                <div>
                  <h3 class="font-semibold text-yellow-800 dark:text-yellow-300">⏳ Enrollment Pending</h3>
                  <p class="text-yellow-700 dark:text-yellow-400 text-sm">
                    Your enrollment request is being reviewed. You'll get access once approved.
                  </p>
                </div>
              </div>

              <div v-else-if="!isEnrolled && !course.is_free" 
                   class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                  <i class="fas fa-lock text-white text-lg"></i>
                </div>
                <div>
                  <h3 class="font-semibold text-blue-800 dark:text-blue-300">🔒 Premium Course</h3>
                  <p class="text-blue-700 dark:text-blue-400 text-sm">
                    Enroll now to get full access to all premium lessons and materials.
                  </p>
                </div>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col gap-3">
              <!-- Enroll Button (for non-enrolled students) -->
              <button v-if="!isEnrolled" 
                      @click="enrollCourse"
                      class="px-8 py-4 bg-gradient-to-r from-green-500 to-blue-600 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 font-semibold text-lg flex items-center gap-3 group">
                <i class="fas fa-user-plus group-hover:scale-110 transition-transform"></i>
                Enroll Now
              </button>
              
              <!-- Progress Button (for enrolled students) -->
              <a v-if="isEnrolled && enrollmentStatus === 'active'" 
                 :href="progressRoute"
                 class="px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all font-semibold text-lg flex items-center gap-3 group text-center">
                <i class="fas fa-chart-line group-hover:scale-110 transition-transform"></i>
                View Progress
              </a>
              
              <!-- Start Learning Button (for enrolled students only) -->
              <button v-if="isEnrolled && enrollmentStatus === 'active'" 
                      @click="startLearning"
                      class="px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all font-semibold text-lg flex items-center gap-3 group">
                <i class="fas fa-play-circle group-hover:scale-110 transition-transform"></i>
                Start Learning
              </button>

              <!-- Contact Admin Button (for pending enrollment) -->
              <button v-if="isEnrolled && enrollmentStatus === 'pending'" 
                      class="px-8 py-4 bg-gradient-to-r from-yellow-500 to-orange-600 text-white rounded-xl shadow-lg font-semibold text-lg flex items-center gap-3 opacity-80 cursor-not-allowed">
                <i class="fas fa-clock"></i>
                Pending Approval
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Course Content -->
        <div class="lg:col-span-2">
          <!-- Lessons Section -->
          <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="p-6 border-b border-border">
              <h2 class="text-2xl font-semibold flex items-center gap-2">
                <i class="fas fa-play-circle text-blue-500"></i>
                Course Lessons
                <span class="text-sm text-gray-500 font-normal">({{ availableLessons.length }} available)</span>
              </h2>
            </div>
            
            <div class="divide-y divide-border">
              <div v-for="(lesson, index) in availableLessons" :key="lesson.id" 
                   class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200 group">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                      <!-- Lesson Number -->
                      <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                        {{ index + 1 }}
                      </div>
                      
                      <h3 class="font-semibold text-lg">{{ lesson.title }}</h3>
                      
                      <!-- Premium Badge -->
                      <span v-if="!lesson.is_free" class="px-2 py-1 bg-purple-500 text-white rounded text-xs flex items-center gap-1">
                        <i class="fas fa-crown"></i>
                        Premium
                      </span>

                      <!-- Free Badge -->
                      <span v-else class="px-2 py-1 bg-green-500 text-white rounded text-xs flex items-center gap-1">
                        <i class="fas fa-unlock"></i>
                        Free
                      </span>
                    </div>
                    
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 ml-11">
                      {{ lesson.description || 'No description available.' }}
                    </p>
                    
                    <div class="flex items-center gap-4 text-xs text-gray-500 ml-11">
                      <span class="flex items-center gap-1">
                        <i class="fas fa-clock"></i>
                        {{ lesson.duration }} min
                      </span>
                      <span v-if="lesson.file_url" class="flex items-center gap-1">
                        <i class="fas fa-file"></i>
                        {{ getFileType(lesson.file_url) }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="flex gap-2 ml-4">
                    <!-- View Lesson Button (only for enrolled students or free lessons) -->
                    <button v-if="hasLessonAccess(lesson)" 
                            @click="viewLesson(lesson)"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-300 transform hover:scale-105 flex items-center gap-2 group">
                      <i class="fas fa-eye group-hover:scale-110 transition-transform"></i>
                      View
                    </button>
                    
                    <!-- Locked Lesson (for non-enrolled students in premium lessons) -->
                    <div v-else class="px-4 py-2 bg-gray-300 text-gray-600 rounded-lg text-sm cursor-not-allowed flex items-center gap-2">
                      <i class="fas fa-lock"></i>
                      Locked
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- No Lessons Message -->
            <div v-if="availableLessons.length === 0" class="text-center py-12 text-gray-500">
              <i class="fas fa-film text-4xl mb-3 opacity-50"></i>
              <p class="text-lg mb-2">No lessons available yet</p>
              <p class="text-sm">Check back later for course content.</p>
            </div>
          </div>

          <!-- Assignments Section -->
          <div v-if="course.assignments.length > 0" class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-border">
              <h2 class="text-2xl font-semibold flex items-center gap-2">
                <i class="fas fa-tasks text-green-500"></i>
                Course Assignments
                <span class="text-sm text-gray-500 font-normal">({{ course.assignments.length }} to complete)</span>
              </h2>
            </div>
            
            <div class="divide-y divide-border">
              <div v-for="assignment in course.assignments" :key="assignment.id" 
                   class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                <h3 class="font-semibold text-lg mb-2 flex items-center gap-2">
                  <i class="fas fa-file-alt text-green-500"></i>
                  {{ assignment.title }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 ml-7">
                  {{ assignment.description }}
                </p>
                <div class="flex items-center justify-between text-xs text-gray-500 ml-7">
                  <span class="flex items-center gap-1">
                    <i class="fas fa-calendar"></i>
                    Due: {{ formatDate(assignment.due_date) }}
                  </span>
                  <button class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition text-xs flex items-center gap-1">
                    <i class="fas fa-paper-plane"></i>
                    Submit
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Course Stats -->
          <div class="bg-card border border-border rounded-2xl p-6 shadow-lg">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
              <i class="fas fa-chart-bar text-blue-500"></i>
              Course Statistics
            </h3>
            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 flex items-center gap-2">
                  <i class="fas fa-play-circle text-blue-500"></i>
                  Total Lessons:
                </span>
                <span class="font-semibold">{{ course.lessons.length }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 flex items-center gap-2">
                  <i class="fas fa-unlock text-green-500"></i>
                  Free Lessons:
                </span>
                <span class="font-semibold">{{ freeLessonsCount }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 flex items-center gap-2">
                  <i class="fas fa-crown text-purple-500"></i>
                  Premium Lessons:
                </span>
                <span class="font-semibold">{{ premiumLessonsCount }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 flex items-center gap-2">
                  <i class="fas fa-clock text-orange-500"></i>
                  Total Duration:
                </span>
                <span class="font-semibold">{{ totalDuration }}</span>
              </div>
            </div>
          </div>

          <!-- Instructor Info -->
          <div class="bg-card border border-border rounded-2xl p-6 shadow-lg">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
              <i class="fas fa-user-tie text-green-500"></i>
              Instructor
            </h3>
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                {{ course.instructor.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <h4 class="font-semibold">{{ course.instructor.name }}</h4>
                <p class="text-gray-500 text-sm">{{ course.instructor.email }}</p>
              </div>
            </div>
          </div>

          <!-- Progress Card (only for enrolled students) -->
          <div v-if="isEnrolled && enrollmentStatus === 'active'" class="bg-card border border-border rounded-2xl p-6 shadow-lg">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
              <i class="fas fa-trophy text-yellow-500"></i>
              Your Progress
            </h3>
            <div class="text-center">
              <!-- Circular Progress -->
              <div class="relative inline-block mb-3">
                <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 100 100">
                  <!-- Background Circle -->
                  <circle cx="50" cy="50" r="40" stroke="#e5e7eb" stroke-width="8" fill="none" />
                  <!-- Progress Circle -->
                  <circle cx="50" cy="50" r="40" stroke="#10b981" :stroke-dasharray="251.2" 
                          :stroke-dashoffset="251.2 - (251.2 * progressPercentage / 100)" 
                          stroke-width="8" fill="none" stroke-linecap="round" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                  <span class="text-xl font-bold">{{ progressPercentage }}%</span>
                </div>
              </div>
              <p class="text-sm text-gray-600">
                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                {{ completedLessonsCount }} of {{ course.lessons.length }} lessons completed
              </p>
            </div>
          </div>

          <!-- Enrollment Card (for non-enrolled students) -->
          <div v-else-if="!isEnrolled" class="bg-card border border-border rounded-2xl p-6 shadow-lg">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
              <i class="fas fa-rocket text-orange-500"></i>
              Get Started
            </h3>
            <p class="text-sm text-gray-600 mb-4">
              Enroll in this course to unlock all lessons and start your learning journey.
            </p>
            <button @click="enrollCourse"
                    class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-blue-600 text-white rounded-xl hover:shadow-xl transform hover:scale-105 transition-all duration-300 font-semibold flex items-center justify-center gap-2">
              <i class="fas fa-user-plus"></i>
              Enroll Now
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import axios from 'axios'

const props = defineProps({
  course: Object,
  enrollment: Object,
  isEnrolled: Boolean,
  enrollRoute: String,
  progressRoute: String,
  csrfToken: String
})

const toast = useToast()

// Computed properties
const coursesIndexRoute = computed(() => '/student/courses')

const availableLessons = computed(() => {
  return props.course.lessons.filter(lesson => !lesson.deleted_at)
})

const freeLessonsCount = computed(() => {
  return availableLessons.value.filter(lesson => lesson.is_free).length
})

const premiumLessonsCount = computed(() => {
  return availableLessons.value.filter(lesson => !lesson.is_free).length
})

const totalDuration = computed(() => {
  const totalMinutes = availableLessons.value.reduce((sum, lesson) => sum + lesson.duration, 0)
  const hours = Math.floor(totalMinutes / 60)
  const minutes = totalMinutes % 60
  return hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`
})

const completedLessonsCount = computed(() => {
  return props.enrollment?.completed_lessons_count || 0
})

const progressPercentage = computed(() => {
  if (availableLessons.value.length === 0) return 0
  return Math.round((completedLessonsCount.value / availableLessons.value.length) * 100)
})

const enrollmentStatus = computed(() => {
  return props.enrollment?.status || 'not_enrolled'
})

// Methods
const getFileType = (fileUrl) => {
  if (!fileUrl) return 'No file'
  const extension = fileUrl.split('.').pop().toLowerCase()
  const typeMap = {
    'mp4': 'Video',
    'mov': 'Video', 
    'avi': 'Video',
    'pdf': 'PDF',
    'doc': 'Document',
    'docx': 'Document'
  }
  return typeMap[extension] || 'File'
}

const formatDate = (dateString) => {
  if (!dateString) return 'No due date'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const hasLessonAccess = (lesson) => {
  return lesson.is_free || (props.isEnrolled && enrollmentStatus.value === 'active')
}

const enrollCourse = async () => {
  try {
    const response = await axios.post(props.enrollRoute, {}, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })

    if (response.data.success) {
      toast.success('🎉 Successfully enrolled in the course!')
      // Reload page to update enrollment status
      setTimeout(() => {
        window.location.reload()
      }, 1500)
    }
  } catch (err) {
    if (err.response?.status === 422) {
      toast.error('You have already enrolled in this course.')
    } else {
      toast.error('Failed to enroll in course. Please try again.')
    }
  }
}

const startLearning = () => {
  if (availableLessons.value.length > 0) {
    const firstAvailableLesson = availableLessons.value[0]
    if (hasLessonAccess(firstAvailableLesson)) {
      viewLesson(firstAvailableLesson)
    } else {
      toast.error('You need to enroll to access premium lessons.')
    }
  } else {
    toast.info('No lessons available yet.')
  }
}

const viewLesson = (lesson) => {
  if (hasLessonAccess(lesson)) {
    window.location.href = `/student/courses/${props.course.id}/lessons/${lesson.id}`
  } else {
    toast.error('You need to enroll in the course to access this lesson.')
  }
}

onMounted(() => {
  console.log('Course loaded:', props.course.title)
})
</script>
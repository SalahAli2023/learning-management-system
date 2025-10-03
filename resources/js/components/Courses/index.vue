<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-500 to-purple-600 bg-clip-text text-transparent">
           <span class="text-amber-50">📚</span>  {{ headerTitle }}
          </h2>
          <p class="text-sm opacity-75 mt-1">{{ headerDescription }}</p>
        </div>
        
        <!-- Create Course Button (for instructors/admins only) -->
        <a v-if="showCreateButton"
            :href="createRoute"
            class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 font-semibold">
          <i class="fas fa-plus"></i>
          New Course
        </a>
      </div>

      <!-- Tabs (for instructors/admins only) -->
      <div v-if="showTabs" class="flex gap-4 mb-6 border-b border-border">
        <button 
          @click="activeTab = 'active'"
          :class="[
            'px-4 py-2 font-semibold transition',
            activeTab === 'active' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'
          ]">
          Active Courses
        </button>
        <button 
          @click="activeTab = 'deleted'"
          :class="[
            'px-4 py-2 font-semibold transition',
            activeTab === 'deleted' ? 'border-b-2 border-red-500 text-red-600' : 'text-gray-500 hover:text-gray-700'
          ]">
          Deleted Courses
        </button>
      </div>

      <!-- Active Courses -->
      <div v-if="activeTab === 'active' || !showTabs">
        <div v-if="displayCourses.length > 0"
              class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
          <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold flex items-center gap-2">
              <i class="fas fa-book-open text-blue-500"></i>
              {{ tableTitle }}
            </h3>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-border">
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Title</th>
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Category</th>
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider" v-if="isStudent">Enrollment Status</th>
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider" v-else>Status</th>
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-border">
                <template v-if="isStudent">
                  <student-course-row 
                    v-for="course in displayCourses"
                    :key="course.id"
                    :course="course"
                    :enrollments="enrollments"
                    @enroll="enrollCourse"
                    @view="viewCourse"
                    @cancel="cancelEnrollment"
                  />
                </template>
                <template v-else>
                  <instructor-course-row 
                    v-for="course in displayCourses"
                    :key="course.id"
                    :course="course"
                    @delete="deleteCourse"
                  />
                </template>
              </tbody>
            </table>
          </div>

          <!-- Pagination for Active Courses -->
          <div v-if="activeTab === 'active' && activeCourses.length > 0" class="p-4 border-t border-border">
            <pagination :links="activeCoursesLinks" />
          </div>
        </div>

        <!-- Empty State -->
        <empty-state v-else
          :title="emptyStateTitle"
          :description="emptyStateDescription"
          :action-url="emptyStateActionUrl"
          :action-text="emptyStateActionText"
        />
      </div>

      <!-- Deleted Courses (for instructors/admins only) -->
      <div v-if="activeTab === 'deleted' && showTabs">
        <div v-if="deletedCourses.length > 0"
              class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
          <div class="p-6 border-b border-border flex justify-between items-center">
            <h3 class="text-lg font-semibold flex items-center gap-2">
              <i class="fas fa-trash-alt text-red-500"></i>
              Deleted Courses
            </h3>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900 dark:to-red-700 border-b border-border">
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Title</th>
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Category</th>
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Deleted At</th>
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-border">
                <tr v-for="course in deletedCourses" :key="course.id" class="hover:bg-red-50 dark:hover:bg-red-900 transition-all duration-200">
                  <td class="px-6 py-4">{{ course.title }}</td>
                  <td class="px-6 py-4">{{ course.category }}</td>
                  <td class="px-6 py-4">{{ formatDate(course.deleted_at) }}</td>
                  <td class="px-6 py-4 flex gap-2">
                    <button @click="restoreCourse(course)" 
                            class="p-2 bg-green-500 text-white rounded hover:bg-green-600 transition"
                            title="Restore Course">
                      <i class="fas fa-undo"></i>
                    </button>
                    <button @click="forceDeleteCourse(course)" 
                            class="p-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                            title="Delete Permanently">
                      <i class="fas fa-times-circle"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination for Deleted Courses -->
          <div v-if="deletedCourses.length > 0" class="p-4 border-t border-border">
            <pagination :links="deletedCoursesLinks" />
          </div>
        </div>

        <div v-else class="text-center py-10 text-gray-500">
          <i class="fas fa-trash text-3xl mb-3"></i>
          <p>No deleted courses.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import EmptyState from '../UI/EmptyState.vue'
import Pagination from '../UI/Pagination.vue'
import InstructorCourseRow from './InstructorCourseRow.vue'
import StudentCourseRow from './StudentCourseRow.vue'
import axios from 'axios'
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

const props = defineProps({
  courses: Object,
  deletedCoursesData: Object, // بيانات الكورسات المحذوفة بشكل منفصل
  createRoute: String,
  csrfToken: String,
  userRole: {
    type: String,
    default: 'student'
  },
  enrollments: {
    type: Array,
    default: () => []
  }
})

const toast = useToast()
const localCourses = ref([...props.courses.data])
const localDeletedCourses = ref(props.deletedCoursesData ? [...props.deletedCoursesData.data] : [])
const localEnrollments = ref([...props.enrollments]) // إضافة reactive enrollments
const activeTab = ref('active')

// Computed properties
const isStudent = computed(() => props.userRole === 'student')
const isInstructor = computed(() => props.userRole === 'instructor')
const isAdmin = computed(() => props.userRole === 'admin')

const showCreateButton = computed(() => !isStudent.value)
const showTabs = computed(() => !isStudent.value)

const activeCourses = computed(() => localCourses.value.filter(c => !c.deleted_at))
const deletedCourses = computed(() => localDeletedCourses.value)

const displayCourses = computed(() => {
  return activeTab.value === 'active' ? activeCourses.value : deletedCourses.value
})

// روابط الباجنيشن المنفصلة
const activeCoursesLinks = computed(() => {
  return props.courses.links || []
})

const deletedCoursesLinks = computed(() => {
  return props.deletedCoursesData?.links || []
})

const headerTitle = computed(() => {
  if (isStudent.value) return ' Available Courses'
  return ' My Courses'
})

const headerDescription = computed(() => {
  if (isStudent.value) return 'Browse and enroll in available courses'
  return 'Manage your learning materials'
})

const tableTitle = computed(() => {
  if (isStudent.value) return 'Available Courses'
  return 'Course Catalog'
})

const emptyStateTitle = computed(() => {
  if (isStudent.value) return 'No courses available'
  return 'No courses yet'
})

const emptyStateDescription = computed(() => {
  if (isStudent.value) return 'Check back later for new courses'
  return 'Start by creating your first course!'
})

const emptyStateActionUrl = computed(() => {
  return isStudent.value ? null : props.createRoute
})

const emptyStateActionText = computed(() => {
  return isStudent.value ? null : 'Create First Course'
})

// Methods
const formatDate = (date) => new Date(date).toLocaleDateString()

const deleteCourse = async (course) => {
  if (!confirm(`Delete course "${course.title}"?`)) return
  try {
    const response = await axios.delete(`/instructor/courses/${course.id}`, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })
    if (response.data.success) {
      toast.success(`Course "${course.title}" deleted successfully!`)
      const index = localCourses.value.findIndex(c => c.id === course.id)
      if (index !== -1) {
        localCourses.value[index].deleted_at = new Date().toISOString()
        // نقل الكورس المحذوف إلى القائمة المنفصلة
        localDeletedCourses.value.unshift({...localCourses.value[index]})
        localCourses.value.splice(index, 1)
      }
    }
  } catch (err) {
    console.error(err)
    toast.error('Failed to delete course, try again.')
  }
}

const restoreCourse = async (course) => {
  try {
    const response = await axios.post(`/instructor/courses/${course.id}/restore`, {}, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })
    if (response.data.success) {
      toast.success(`Course "${course.title}" restored!`)
      const index = localDeletedCourses.value.findIndex(c => c.id === course.id)
      if (index !== -1) {
        localDeletedCourses.value[index].deleted_at = null
        // إرجاع الكورس إلى القائمة النشطة
        localCourses.value.unshift({...localDeletedCourses.value[index]})
        localDeletedCourses.value.splice(index, 1)
      }
    }
  } catch (err) {
    console.error(err)
    toast.error('Failed to restore course.')
  }
}

const forceDeleteCourse = async (course) => {
  if (!confirm(`Permanently delete course "${course.title}"? This cannot be undone.`)) return
  try {
    const response = await axios.delete(`/instructor/courses/${course.id}/force`, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })
    if (response.data.success) {
      toast.success(`Course "${course.title}" permanently deleted!`)
      localDeletedCourses.value = localDeletedCourses.value.filter(c => c.id !== course.id)
    }
  } catch (err) {
    console.error(err)
    toast.error('Failed to permanently delete course.')
  }
}

const enrollCourse = async (course) => {
  try {
    const response = await axios.post(`/student/courses/${course.id}/enroll`, {}, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })
    
    if (response.data.success) {
      toast.success(`Successfully enrolled in "${course.title}"!`)
      
      // تحديث الـ enrollments محلياً - التحديث المباشر
      localEnrollments.value.push({
        id: response.data.enrollment.id,
        course_id: course.id,
        status: response.data.enrollment.status || 'active'
      })
      
      // إعادة تحميل الصفحة بعد ثانيتين للتأكد من التحديث
      setTimeout(() => {
        window.location.reload()
      }, 2000)
    }
  } catch (err) {
    if (err.response?.status === 422) {
      toast.error('You are already enrolled in this course.')
    } else {
      toast.error('Failed to enroll in course.')
    }
  }
}

const cancelEnrollment = async (course) => {
  try {
    const response = await axios.post(`/student/courses/${course.id}/cancel`, {}, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })
    
    if (response.data.success) {
      toast.success(`Enrollment cancelled for "${course.title}"!`)
      
      // تحديث الـ enrollments محلياً - إزالة ال enrollment
      localEnrollments.value = localEnrollments.value.filter(e => e.course_id !== course.id)
      
      // إعادة تحميل الصفحة بعد ثانيتين للتأكد من التحديث
      setTimeout(() => {
        window.location.reload()
      }, 2000)
    }
  } catch (err) {
    if (err.response?.status === 404) {
      toast.error('Enrollment not found.')
    } else {
      toast.error('Failed to cancel enrollment.')
    }
  }
}

const viewCourse = (course) => {
  if (isStudent.value) {
    window.location.href = `/student/courses/${course.id}`
  } else {
    window.location.href = `/instructor/courses/${course.id}`
  }
}
</script>
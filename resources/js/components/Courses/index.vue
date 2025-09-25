<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-500 to-purple-600 bg-clip-text text-transparent">
            📚 My Courses
          </h2>
          <p class="text-sm opacity-75 mt-1">Manage your learning materials</p>
        </div>
        <a :href="createRoute"
           class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 font-semibold">
          <i class="fas fa-plus"></i>
          New Course
        </a>
      </div>

      <!-- Tabs -->
      <div class="flex gap-4 mb-6 border-b border-border">
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
      <div v-if="activeTab === 'active'">
        <div v-if="activeCourses.length > 0"
              class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
          <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold flex items-center gap-2">
              <i class="fas fa-book-open text-blue-500"></i>
              Course Catalog
            </h3>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-border">
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Title</th>
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Category</th>
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-border">
                <course-row v-for="c in activeCourses"
                            :key="c.id"
                            :course="c"
                            @delete="deleteCourse" />
              </tbody>
            </table>
          </div>
        </div>

        <empty-state v-else
                      title="No courses yet"
                      description="Start by creating your first course!"
                      :action-url="createRoute"
                      action-text="Create First Course" />
      </div>

      <!-- Deleted Courses -->
      <div v-if="activeTab === 'deleted'">
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
                <tr v-for="c in deletedCourses" :key="c.id">
                  <td class="px-6 py-4">{{ c.title }}</td>
                  <td class="px-6 py-4">{{ c.category }}</td>
                  <td class="px-6 py-4">{{ formatDate(c.deleted_at) }}</td>
                  <td class="px-6 py-4 flex gap-2">
                    <button @click="restoreCourse(c)" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                      Restore
                    </button>
                    <button @click="forceDeleteCourse(c)" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                      Delete Permanently
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-else class="text-center py-10 text-gray-500">
          <i class="fas fa-trash text-3xl mb-3"></i>
          <p>No deleted courses.</p>
        </div>
      </div>

      <!-- Pagination -->
      <pagination :links="courses.links" />
    </div>
  </div>
</template>

<script setup>
import EmptyState from '../UI/EmptyState.vue'
import Pagination from '../UI/Pagination.vue'
import CourseRow from '../CourseRow.vue'
import axios from 'axios'
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

const props = defineProps({
  courses: Object,
  createRoute: String,
  csrfToken: String,
})

const toast = useToast()
const localCourses = ref([...props.courses.data])
const activeTab = ref('active')

const activeCourses = computed(() => localCourses.value.filter(c => !c.deleted_at))
const deletedCourses = computed(() => localCourses.value.filter(c => c.deleted_at))

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
      const index = localCourses.value.findIndex(c => c.id === course.id)
      if (index !== -1) {
        localCourses.value[index].deleted_at = null
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
      localCourses.value = localCourses.value.filter(c => c.id !== course.id)
    }
  } catch (err) {
    console.error(err)
    toast.error('Failed to permanently delete course.')
  }
}
</script>

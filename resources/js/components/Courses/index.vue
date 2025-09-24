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

      <!-- Alerts -->

      <!-- Courses Table -->
      <div v-if="localCourses.length > 0"
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
              <course-row v-for="c in localCourses"
                          :key="c.id"
                          :course="c"
                          @delete="deleteCourse" />
            </tbody>
          </table>
        </div>
      </div>

      <!-- Empty State -->
      <empty-state v-else
                    title="No courses yet"
                    description="Start by creating your first course!"
                    :action-url="createRoute"
                    action-text="Create First Course" />

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
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

const props = defineProps({
  courses: Object,
  createRoute: String,
})

const toast = useToast()
const localCourses = ref([...props.courses.data])

const deleteCourse = async (course) => {
  if (!confirm(`Delete course "${course.title}"?`)) return
  try {
      const response=await axios.delete(`/instructor/courses/${course.id}`, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    });
    if (response.data.success) {    
      toast.success(`Course "${course.title}" deleted successfully!`)
      // نحذف الكورس من القائمة بدون تحديث الصفحة
      localCourses.value = localCourses.value.filter(c => c.id !== course.id)
    }
  } catch (err) {
    console.error(err)
    toast.error('Failed to delete course, try again.')
  }
}
</script>
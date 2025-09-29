<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h2 class="text-3xl font-bold bg-gradient-to-r from-green-400 to-blue-500 bg-clip-text text-transparent">
            <i class="fas fa-film text-green-400 mr-2"></i>
            Lessons Management
          </h2>
          <p class="text-sm opacity-75 mt-1">Manage lessons for: <strong>{{ course.title }}</strong></p>
        </div>
        <div class="flex gap-3">
          <a :href="createRoute"
            class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-blue-600 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 font-semibold">
            <i class="fas fa-plus"></i>
            New Lesson
          </a>
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
            <i class="fas fa-film text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ totalLessons }}</h3>
          <p class="text-sm opacity-75">Total Lessons</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-unlock text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ freeLessons }}</h3>
          <p class="text-sm opacity-75">Free Lessons</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-crown text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ premiumLessons }}</h3>
          <p class="text-sm opacity-75">Premium Lessons</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 text-center shadow-lg">
          <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-clock text-white text-xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-text">{{ totalDuration }}</h3>
          <p class="text-sm opacity-75">Total Duration</p>
        </div>
      </div>

      <!-- Lessons Table -->
      <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <h3 class="text-lg font-semibold flex items-center gap-2">
            <i class="fas fa-list text-blue-500"></i>
            Lessons List ({{ localLessons.length }})
          </h3>
          
          <!-- Filters -->
          <div class="flex gap-3">
            <select v-model="filterStatus" 
                    class="px-4 py-2 border-2 border-border rounded-xl bg-bg text-text focus:border-blue-500 transition-all">
              <option value="all">All Lessons</option>
              <option value="free">Free Only</option>
              <option value="premium">Premium Only</option>
            </select>
            
            <select v-model="sortBy" 
                    class="px-4 py-2 border-2 border-border rounded-xl bg-bg text-text focus:border-blue-500 transition-all">
              <option value="order">Sort by Order</option>
              <option value="title">Sort by Title</option>
              <option value="duration">Sort by Duration</option>
            </select>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-border">
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Order</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Title</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Duration</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">File</th>
                <th class="px-6 py-4 text-left font-semibold text-sm uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr v-for="lesson in filteredLessons" :key="lesson.id" 
                  class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                <td class="px-6 py-4 font-bold text-blue-500">#{{ lesson.order }}</td>
                <td class="px-6 py-4">
                  <div class="font-medium text-text">{{ lesson.title }}</div>
                  <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">{{ lesson.description }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <i class="fas fa-clock text-orange-500"></i>
                    {{ lesson.duration }} min
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold transition-all',
                    lesson.is_free 
                      ? 'bg-green-100 text-green-600 border border-green-200' 
                      : 'bg-purple-100 text-purple-600 border border-purple-200'
                  ]">
                    <i :class="lesson.is_free ? 'fas fa-unlock mr-1' : 'fas fa-crown mr-1'"></i>
                    {{ lesson.is_free ? 'Free' : 'Premium' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div v-if="lesson.file_url" class="flex items-center gap-2 text-green-500">
                    <i class="fas fa-file"></i>
                    <span class="text-xs">Attached</span>
                  </div>
                  <div v-else class="flex items-center gap-2 text-gray-400">
                    <i class="fas fa-times"></i>
                    <span class="text-xs">No File</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <a :href="getViewRoute(lesson)" 
                       class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-300 tooltip"
                       title="View Lesson">
                      <i class="fas fa-eye"></i>
                    </a>
                    
                    <a :href="getEditRoute(lesson)" 
                       class="p-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-300 tooltip"
                       title="Edit Lesson">
                      <i class="fas fa-edit"></i>
                    </a>
                    
                    <button @click="deleteLesson(lesson)" 
                            class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-300 tooltip"
                            title="Delete Lesson">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="filteredLessons.length === 0" class="text-center py-12 text-gray-500">
          <i class="fas fa-film text-4xl mb-3 opacity-50"></i>
          <p class="text-lg mb-2">No lessons found</p>
          <p class="text-sm opacity-75 mb-4">Try changing your filters or create a new lesson</p>
          <a :href="createRoute"
             class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-blue-600 text-white rounded-xl shadow-lg hover:shadow-xl transition">
            <i class="fas fa-plus"></i>
            Create First Lesson
          </a>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-6">
        <Pagination :links="lessons.links" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'
import Pagination from '../UI/Pagination.vue'

const props = defineProps({
  lessons: Object,
  course: Object,
  createRoute: String,
  courseRoute: String,
  csrfToken: String
})

const toast = useToast()
const localLessons = ref([...props.lessons.data])
const filterStatus = ref('all')
const sortBy = ref('order')
const isDeleting = ref(false)

// Computed properties
const totalLessons = computed(() => localLessons.value.length)
const freeLessons = computed(() => localLessons.value.filter(lesson => lesson.is_free).length)
const premiumLessons = computed(() => localLessons.value.filter(lesson => !lesson.is_free).length)
const totalDuration = computed(() => {
  const total = localLessons.value.reduce((sum, lesson) => sum + lesson.duration, 0)
  const hours = Math.floor(total / 60)
  const minutes = total % 60
  return hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`
})

const filteredLessons = computed(() => {
  let filtered = [...localLessons.value]
  
  // Filter by status
  if (filterStatus.value === 'free') {
    filtered = filtered.filter(lesson => lesson.is_free)
  } else if (filterStatus.value === 'premium') {
    filtered = filtered.filter(lesson => !lesson.is_free)
  }
  
  // Sort lessons
  switch (sortBy.value) {
    case 'title':
      filtered.sort((a, b) => a.title.localeCompare(b.title))
      break
    case 'duration':
      filtered.sort((a, b) => b.duration - a.duration)
      break
    case 'order':
    default:
      filtered.sort((a, b) => a.order - b.order)
      break
  }
  
  return filtered
})

// Methods
const getViewRoute = (lesson) => {
  return `/instructor/courses/${props.course.id}/lessons/${lesson.id}`
}

const getEditRoute = (lesson) => {
  return `/instructor/courses/${props.course.id}/lessons/${lesson.id}/edit`
}

const deleteLesson = async (lesson) => {
  if (!confirm(`Are you sure you want to delete "${lesson.title}"? This action can be undone.`)) return
  
  isDeleting.value = true
  try {
    const response = await axios.delete(`/instructor/courses/${props.course.id}/lessons/${lesson.id}`, {
      headers: { 'X-CSRF-TOKEN': props.csrfToken }
    })
    
    if (response.data.success) {
      toast.success(`Lesson "${lesson.title}" has been moved to trash!`)
      localLessons.value = localLessons.value.filter(l => l.id !== lesson.id)
    }
  } catch (err) {
    console.error('Delete error:', err)
    if (err.response?.status === 403) {
      toast.error('You are not authorized to delete this lesson.')
    } else {
      toast.error('Failed to delete lesson. Please try again.')
    }
  } finally {
    isDeleting.value = false
  }
}

// Initialize
onMounted(() => {
  console.log('Lessons loaded:', localLessons.value.length)
})
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
<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-5xl mx-auto px-4 py-8">

      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="indexRoute" class="text-text hover:text-blue-500 flex items-center gap-1">
          <i class="fas fa-home"></i>
          Lessons
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-blue-500 font-medium">{{ lesson.title }}</span>
      </nav>

      <!-- Lesson Card -->
      <div class="bg-card border border-border rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
          <div>
            <h1 class="text-3xl font-bold mb-2">{{ lesson.title }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ lesson.description || 'No description provided.' }}</p>
            <div class="mt-4 flex gap-3">
              <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                {{ lesson.duration }} min
              </span>
              <span :class="lesson.is_free 
                ? 'px-3 py-1 bg-green-100 text-green-600 rounded text-xs' 
                : 'px-3 py-1 bg-purple-100 text-purple-600 rounded text-xs'">
                {{ lesson.is_free ? 'Free' : 'Premium' }}
              </span>
            </div>
            <div v-if="lesson.file_url" class="mt-4">
              <a :href="lesson.file_url" target="_blank"
                 class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                <i class="fas fa-download"></i> Download File
              </a>
            </div>
          </div>
          <div class="flex gap-3">
            <a :href="editRoute" class="px-6 py-3 bg-green-500 text-white rounded-xl shadow hover:shadow-lg">
              <i class="fas fa-edit"></i> Edit
            </a>
            <a :href="indexRoute" class="px-6 py-3 border border-border rounded-xl hover:bg-gray-50">
              <i class="fas fa-arrow-left"></i> Back
            </a>
          </div>
        </div>
      </div>

      <!-- Meta Info -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-8">
        <div class="bg-card border border-border rounded-2xl p-6 shadow">
          <h4 class="font-semibold mb-2">Created At</h4>
          <p>{{ formatDate(lesson.created_at) }}</p>
        </div>
        <div class="bg-card border border-border rounded-2xl p-6 shadow">
          <h4 class="font-semibold mb-2">Last Updated</h4>
          <p>{{ formatDate(lesson.updated_at) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  lesson: Object,
  indexRoute: String,
  editRoute: String
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 py-8">
      
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="indexRoute" class="text-text hover:text-purple-500 flex items-center gap-1">
          <i class="fas fa-home"></i>
          Enrollments
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-purple-500 font-medium">Enrollment #{{ enrollment.id }}</span>
      </nav>

      <!-- Enrollment Card -->
      <div class="bg-card border border-border rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8 space-y-6">
          <!-- Student -->
          <div>
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
              <i class="fas fa-user text-purple-500"></i>
              Student
            </h3>
            <p class="text-gray-700 dark:text-gray-300">{{ enrollment.student?.name }}</p>
          </div>

          <!-- Course -->
          <div>
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
              <i class="fas fa-book text-blue-500"></i>
              Course
            </h3>
            <p class="text-gray-700 dark:text-gray-300">{{ enrollment.course?.title }}</p>
          </div>

          <!-- Status -->
          <div>
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
              <i class="fas fa-info-circle text-green-500"></i>
              Status
            </h3>
            <span :class="statusClass(enrollment.status)">
              {{ enrollment.status }}
            </span>
          </div>

          <!-- Dates -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
              <h4 class="font-semibold mb-2">Enrolled At</h4>
              <p>{{ formatDate(enrollment.created_at) }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
              <h4 class="font-semibold mb-2">Last Updated</h4>
              <p>{{ formatDate(enrollment.updated_at) }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4 border-t border-border">
            <a :href="editRoute" 
              class="px-6 py-3 bg-green-500 text-white rounded-xl shadow hover:shadow-lg">
              <i class="fas fa-edit"></i> Edit
            </a>
            <a :href="indexRoute" 
              class="px-6 py-3 border border-border rounded-xl hover:bg-gray-50">
              <i class="fas fa-arrow-left"></i> Back
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  enrollment: { type: Object, required: true },
  indexRoute: { type: String, required: true },
  editRoute: { type: String, required: true }
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const statusClass = (status) => {
  switch (status) {
    case 'active':
      return 'px-3 py-1 rounded-full text-xs bg-green-100 text-green-600'
    case 'completed':
      return 'px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-600'
    case 'pending':
      return 'px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-600'
    default:
      return 'px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-600'
  }
}
</script>
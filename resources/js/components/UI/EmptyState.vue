<template>
  <div class="text-center py-12 bg-card border border-border rounded-2xl shadow-lg">
    <i class="fas fa-book-open text-6xl text-gray-400 mb-4"></i>
    <h3 class="text-xl font-semibold mb-2">{{ title }}</h3>
    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ description }}</p>
    <a :href="actionUrl" 
       class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors">
      <i class="fas fa-plus"></i>
      {{ actionText }}
    </a>
  </div>
</template>

<script setup>
const props = defineProps({
  title: String,
  description: String,
  actionUrl: String,
  actionText: String
})
</script>

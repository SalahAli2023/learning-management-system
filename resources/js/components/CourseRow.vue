<!-- <template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200 group">
    <td class="px-6 py-4 whitespace-nowrap">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
          <i class="fas fa-book text-white text-sm"></i>
        </div>
        <div>
          <div class="font-semibold group-hover:text-blue-500 transition-colors">{{ course.title }}</div>
          <div class="text-xs opacity-75">{{ course.created_at }}</div>
        </div>
      </div>
    </td>

    <td class="px-6 py-4 whitespace-nowrap">
      <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
        <i class="fas fa-hashtag"></i>
        {{ course.category }}
      </span>
    </td>

    <td class="px-6 py-4 whitespace-nowrap">
      <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium shadow-lg"
            :class="course.is_published 
              ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-300 dark:border-green-700' 
              : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-700'">
        <i :class="course.is_published ? 'fas fa-check-circle' : 'fas fa-pencil-alt'"></i>
        {{ course.is_published ? 'Published' : 'Draft' }}
      </span>
    </td>

    <td class="px-6 py-4 whitespace-nowrap">
      <div class="flex items-center gap-2">
        <a :href="`/instructor/courses/${course.id}`"
           class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:scale-110 transition-all">
          <i class="fas fa-eye"></i>
        </a>
        <a :href="`/instructor/courses/${course.id}/edit`"
           class="p-2 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 hover:scale-110 transition-all">
          <i class="fas fa-edit"></i>
        </a>
        <button @click="$emit('delete', course)"
                class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:scale-110 transition-all">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </td>
  </tr>
</template>

<script setup>
const props = defineProps({ course: Object })
</script> -->

<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200 group">
    <!-- Title -->
    <td class="px-6 py-4 whitespace-nowrap">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
          <i class="fas fa-book text-white text-sm"></i>
        </div>
        <div>
          <div class="font-semibold group-hover:text-blue-500 transition-colors">{{ course.title }}</div>
          <div class="text-xs opacity-75">{{ new Date(course.created_at).toLocaleDateString() }}</div>
        </div>
      </div>
    </td>

    <!-- Category -->
    <td class="px-6 py-4 whitespace-nowrap">
      <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
        <i class="fas fa-hashtag"></i>
        {{ course.category }}
      </span>
    </td>

    <!-- Status -->
    <td class="px-6 py-4 whitespace-nowrap">
      <span
        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium shadow-lg transition-transform duration-200 hover:scale-105"
        :class="course.is_published 
          ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-300 dark:border-green-700' 
          : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-700'">
        <i :class="course.is_published ? 'fas fa-check-circle' : 'fas fa-pencil-alt'"></i>
        {{ course.is_published ? 'Published' : 'Draft' }}
      </span>
    </td>

    <!-- Actions -->
    <td class="px-6 py-4 whitespace-nowrap">
      <div class="flex items-center gap-2">
        <!-- View -->
        <a :href="`/instructor/courses/${course.id}`"
           class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all duration-200 transform hover:scale-110"
           title="View">
          <i class="fas fa-eye"></i>
        </a>

        <!-- Edit -->
        <a :href="`/instructor/courses/${course.id}/edit`"
           class="p-2 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800 hover:bg-green-100 dark:hover:bg-green-900/30 transition-all duration-200 transform hover:scale-110"
           title="Edit">
          <i class="fas fa-edit"></i>
        </a>

        <!-- Delete -->
        <button @click="$emit('delete', course)"
                class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-200 transform hover:scale-110"
                title="Delete">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </td>
  </tr>
</template>

<script setup>
defineProps({
  course: { type: Object, required: true }
})
</script>




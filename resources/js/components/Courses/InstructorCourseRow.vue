<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
    <td class="px-6 py-4">
      <div class="font-medium text-text">{{ course.title }}</div>
    </td>
    <td class="px-6 py-4">
      <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-xs font-semibold">
        {{ course.category }}
      </span>
    </td>
    <td class="px-6 py-4">
      <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-semibold">
        {{ course.is_published ? 'Published' : 'Draft' }}
      </span>
    </td>
    <td class="px-6 py-4">
      <div class="flex gap-2">
        <a :href="viewRoute" 
           class="p-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-sm"
           title="View Course">
          <i class="fas fa-eye"></i>
        </a>
        <a :href="editRoute" 
           class="p-2 bg-green-500 text-white rounded hover:bg-green-600 transition text-sm"
           title="Edit Course">
          <i class="fas fa-edit"></i>
        </a>
        <button @click="$emit('delete', course)" 
                class="p-2 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm"
                title="Delete Course">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </td>
  </tr>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  course: Object
})

const emit = defineEmits(['delete'])

const viewRoute = computed(() => {
  return `/instructor/courses/${props.course.id}`
})

const editRoute = computed(() => {
  return `/instructor/courses/${props.course.id}/edit`
})
</script>
<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
    <td class="px-6 py-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
          <i class="fas fa-book text-white text-sm"></i>
        </div>
        <div>
          <div class="font-medium text-text">{{ course.title }}</div>
          <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
            <i class="fas fa-user-tie text-blue-500"></i>
            {{ course.instructor?.name }}
          </div>
        </div>
      </div>
    </td>
    
    <td class="px-6 py-4">
      <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-xs font-semibold flex items-center gap-1 w-fit">
        <i class="fas fa-tag"></i>
        {{ course.category }}
      </span>
    </td>
    
    <td class="px-6 py-4">
      <span :class="enrollmentStatusClass" class="text-xs font-semibold flex items-center gap-1 w-fit">
        <i :class="enrollmentStatusIcon"></i>
        {{ enrollmentStatusText }}
      </span>
    </td>
    
    <td class="px-6 py-4">
      <div class="flex gap-2">
        <!-- View Course Button - Available for everyone -->
        <button @click="$emit('view', course)" 
                class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-300 transform hover:scale-110 tooltip"
                title="View Course Details">
          <i class="fas fa-eye"></i>
        </button>
        
        <!-- Enroll Button - Only for non-enrolled students -->
        <button v-if="!isEnrolled" 
                @click="$emit('enroll', course)"
                class="p-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-300 transform hover:scale-110 tooltip"
                title="Enroll in Course">
          <i class="fas fa-user-plus"></i>
        </button>
        
        <!-- Cancel Button - For enrolled students (active or pending) -->
        <button v-else-if="isEnrolled && (enrollmentStatus === 'active' || enrollmentStatus === 'pending')"
                @click="confirmCancel"
                class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-300 transform hover:scale-110 tooltip"
                title="Cancel Enrollment">
          <i class="fas fa-times-circle"></i>
        </button>

        <!-- Completed Status - For completed enrollment -->
        <span v-else-if="isEnrolled && enrollmentStatus === 'completed'"
              class="p-2 bg-purple-500 text-white rounded-lg flex items-center gap-1 tooltip"
              title="Course Completed">
          <i class="fas fa-graduation-cap"></i>
        </span>

        <!-- Cancelled Status - For cancelled enrollment -->
        <span v-else-if="isEnrolled && enrollmentStatus === 'cancelled'"
              class="p-2 bg-gray-500 text-white rounded-lg flex items-center gap-1 tooltip"
              title="Enrollment Cancelled">
          <i class="fas fa-ban"></i>
        </span>
      </div>
    </td>
  </tr>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  course: Object,
  enrollments: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['enroll', 'view', 'cancel'])

// Find enrollment for this course
const courseEnrollment = computed(() => {
  return props.enrollments.find(e => e.course_id === props.course.id)
})

const isEnrolled = computed(() => !!courseEnrollment.value)

const enrollmentStatus = computed(() => {
  return courseEnrollment.value?.status || 'not_enrolled'
})

const enrollmentStatusText = computed(() => {
  const statusMap = {
    'not_enrolled': 'Not Enrolled',
    'pending': 'Pending Approval',
    'active': 'Enrolled',
    'completed': 'Completed',
    'cancelled': 'Cancelled'
  }
  return statusMap[enrollmentStatus.value] || 'Not Enrolled'
})

const enrollmentStatusClass = computed(() => {
  const classMap = {
    'not_enrolled': 'px-3 py-1 bg-gray-100 text-gray-600 rounded-full',
    'pending': 'px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full',
    'active': 'px-3 py-1 bg-green-100 text-green-600 rounded-full',
    'completed': 'px-3 py-1 bg-purple-100 text-purple-600 rounded-full',
    'cancelled': 'px-3 py-1 bg-red-100 text-red-600 rounded-full'
  }
  return classMap[enrollmentStatus.value] || classMap.not_enrolled
})

const enrollmentStatusIcon = computed(() => {
  const iconMap = {
    'not_enrolled': 'fas fa-times-circle',
    'pending': 'fas fa-clock',
    'active': 'fas fa-check-circle',
    'completed': 'fas fa-graduation-cap',
    'cancelled': 'fas fa-ban'
  }
  return iconMap[enrollmentStatus.value] || 'fas fa-times-circle'
})

const confirmCancel = () => {
  if (confirm('Are you sure you want to cancel your enrollment? This action cannot be undone.')) {
    emit('cancel', props.course)
  }
}
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
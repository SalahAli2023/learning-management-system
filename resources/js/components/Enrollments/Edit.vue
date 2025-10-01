<template>
  <div class="bg-bg text-text transition-colors duration-300">
    <div class="max-w-3xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8 text-center">
        <a :href="indexRoute" class="flex items-center gap-2 text-text hover:text-purple-500 mb-4 group">
          <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
          Back to Enrollments
        </a>
        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-500 to-pink-600 bg-clip-text text-transparent">
          Edit Enrollment
        </h1>
        <p class="text-sm opacity-75">Update student enrollment</p>
      </div>

      <!-- Form -->
      <div class="bg-card border border-border rounded-2xl shadow-lg">
        <form @submit.prevent="submitForm" class="p-6 space-y-6">
          <!-- Student -->
          <div>
            <label class="block font-semibold mb-2">Student *</label>
            <select v-model="form.student_id" required
              class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text">
              <option disabled value="">Select a student</option>
              <option v-for="s in students" :key="s.id" :value="s.id">
                {{ s.name }}
              </option>
            </select>
          </div>

          <!-- Course -->
          <div>
            <label class="block font-semibold mb-2">Course *</label>
            <select v-model="form.course_id" required
              class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text">
              <option disabled value="">Select a course</option>
              <option v-for="c in courses" :key="c.id" :value="c.id">
                {{ c.title }}
              </option>
            </select>
          </div>

          <!-- Status -->
          <div>
            <label class="block font-semibold mb-2">Status</label>
            <select v-model="form.status" class="w-full px-4 py-3 border-2 border-border rounded-xl bg-bg text-text">
              <option value="active">Active</option>
              <option value="completed">Completed</option>
              <option value="pending">Pending</option>
            </select>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4 border-t border-border">
            <button type="submit" :disabled="isSubmitting"
              class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-semibold disabled:opacity-50">
              <i class="fas fa-save" :class="{ 'fa-spin': isSubmitting }"></i>
              {{ isSubmitting ? 'Updating...' : 'Update Enrollment' }}
            </button>
            <a :href="indexRoute" class="px-6 py-3 border-2 border-border rounded-xl hover:bg-gray-100">Cancel</a>
          </div>
        </form>
      </div>
    </div>
    <toast-container />
  </div>
</template>

<script setup>
import { reactive, ref } from "vue"
import axios from "axios"
import { useToast } from "vue-toastification"

const props = defineProps({
  enrollment: Object,
  updateRoute: String,
  indexRoute: String,
  students: Array,
  courses: Array,
  csrfToken: String,
})

const form = reactive({
  student_id: props.enrollment.student_id,
  course_id: props.enrollment.course_id,
  status: props.enrollment.status,
})

const isSubmitting = ref(false)
const toast = useToast()

const submitForm = async () => {
  isSubmitting.value = true
  try {
    await axios.put(props.updateRoute, form, {
      headers: { "X-CSRF-TOKEN": props.csrfToken },
    })
    toast.success("Enrollment updated successfully!")
    setTimeout(() => (window.location.href = props.indexRoute), 1000)
  } catch (err) {
    console.error(err.response?.data || err)
    toast.error("Failed to update enrollment.")
  } finally {
    isSubmitting.value = false
  }
}
</script>

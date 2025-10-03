<template>
    <div class="min-h-screen bg-bg text-text transition-colors duration-300">
        <div class="max-w-4xl mx-auto px-4 py-8">
        
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm mb-8">
                <a :href="assignmentsRoute" class="text-text hover:text-blue-500 flex items-center gap-1">
                    <i class="fas fa-arrow-left"></i>
                    Back to Assignments
                </a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <span class="text-blue-500 font-medium">Submission Details</span>
            </nav>

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-500 to-pink-600 bg-clip-text text-transparent">
                        <span class="text-amber-50">📄</span> Submission Details
                    </h2>
                    <p class="text-sm opacity-75 mt-1">
                        <strong>{{ assignment.title }}</strong> - {{ course.title }}
                    </p>
                </div>
                
                <!-- Submission Status -->
                <div class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-blue-800 dark:text-blue-300">Your Submission</h3>
                        <p class="text-blue-700 dark:text-blue-400 text-sm">
                            Submitted {{ formatDateTime(submission.created_at) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Submission Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Submission Info -->
                    <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-border">
                        <h3 class="text-xl font-semibold flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Submission Information
                        </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-500">Assignment</label>
                                    <p class="text-lg font-medium">{{ assignment.title }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-500">Submitted On</label>
                                    <p class="text-lg font-medium">{{ formatDateTime(submission.created_at) }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-500">Status</label>
                                    <p>
                                        <span :class="statusBadgeClass" class="text-sm font-semibold">
                                        <i :class="statusIcon" class="mr-1"></i>
                                        {{ statusText }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-500">Due Date</label>
                                    <p class="text-lg" :class="dueDateClass">
                                        {{ formattedDueDate }}
                                        <span v-if="isLate" class="text-xs bg-red-500 text-white px-2 py-1 rounded ml-2">
                                        Late
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submitted Content -->
                    <div v-if="submission.submission_text" class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-border">
                            <h3 class="text-xl font-semibold flex items-center gap-2">
                                <i class="fas fa-file-alt text-green-500"></i>
                                Your Submission Text
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="prose dark:prose-invert max-w-none bg-white dark:bg-gray-800 rounded-xl p-6 border">
                                <p class="whitespace-pre-wrap">{{ submission.submission_text }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submitted File -->
                    <div v-if="submission.file_url" class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-border">
                            <h3 class="text-xl font-semibold flex items-center gap-2">
                                <i class="fas fa-paperclip text-orange-500"></i>
                                Attached File
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                                <i class="fas fa-file text-green-500 text-2xl"></i>
                                <div class="flex-1">
                                    <p class="font-semibold">{{ getFileName(submission.file_url) }}</p>
                                    <p class="text-sm text-gray-500">Submitted file</p>
                                </div>
                                <a :href="getDownloadRoute" 
                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center gap-2">
                                    <i class="fas fa-download"></i>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grade & Feedback -->
                <div class="space-y-6">
                    <!-- Grade Card -->
                    <div v-if="submission.grade !== null" class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-border">
                            <h3 class="text-xl font-semibold flex items-center gap-2">
                                <i class="fas fa-star text-yellow-500"></i>
                                Your Grade
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center">
                                <div class="text-4xl font-bold mb-2" :class="gradeColorClass">
                                    {{ submission.grade }}
                                </div>
                                <div class="text-sm text-gray-500 mb-4">out of 100</div>
                                
                                <!-- Grade Progress -->
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-4">
                                    <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" 
                                        :style="{ width: submission.grade + '%' }"></div>
                                </div>
                                
                                <p class="text-sm font-semibold" :class="gradeColorClass">
                                    {{ gradeRemarks }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Card -->
                    <div v-if="submission.feedback" class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-border">
                        <h3 class="text-xl font-semibold flex items-center gap-2">
                            <i class="fas fa-comment-dots text-green-500"></i>
                            Instructor Feedback
                        </h3>
                        </div>
                        <div class="p-6">
                        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                            <p class="whitespace-pre-wrap">{{ submission.feedback }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                            <i class="fas fa-user-tie"></i>
                            <span>Graded by instructor</span>
                            <span v-if="submission.graded_at">on {{ formatDate(submission.graded_at) }}</span>
                        </div>
                        </div>
                    </div>

                    <!-- Pending Grade -->
                    <div v-else class="bg-card border border-border rounded-2xl p-6 shadow-lg">
                        <div class="text-center">
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-blue-800 dark:text-blue-300 mb-2">Awaiting Grade</h3>
                        <p class="text-blue-700 dark:text-blue-400 text-sm">
                            Your submission is under review. You'll receive a grade and feedback soon.
                        </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-card border border-border rounded-2xl p-6 shadow-lg">
                        <h3 class="font-semibold mb-4 flex items-center gap-2">
                        <i class="fas fa-tasks text-purple-500"></i>
                        Actions
                        </h3>
                        <div class="space-y-3">
                        <a :href="getResubmitRoute" 
                            class="w-full px-4 py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition-all font-semibold flex items-center justify-center gap-2">
                            <i class="fas fa-redo"></i>
                            Resubmit Assignment
                        </a>
                        
                        <a :href="getAssignmentRoute" 
                            class="w-full px-4 py-3 border-2 border-border text-text rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all font-semibold flex items-center justify-center gap-2">
                            <i class="fas fa-eye"></i>
                            View Assignment
                        </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useToast } from 'vue-toastification'

const props = defineProps({
    course: Object,
    assignment: Object,
    submission: Object,
    assignmentsRoute: String,
    csrfToken: String
})

const toast = useToast()

// Computed properties
const formattedDueDate = computed(() => {
    if (!props.assignment.due_date) return 'No due date'
        return new Date(props.assignment.due_date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
    })
})

const dueDateClass = computed(() => {
    if (!props.assignment.due_date) return 'text-gray-500'
    return isLate.value ? 'text-red-500' : 'text-green-500'
})

const isLate = computed(() => {
    if (!props.assignment.due_date) return false
    return new Date(props.submission.created_at) > new Date(props.assignment.due_date)
})

const statusText = computed(() => {
    return props.submission.grade !== null ? 'Graded' : 'Under Review'
})

const statusBadgeClass = computed(() => {
    return props.submission.grade !== null 
        ? 'px-3 py-1 bg-green-100 text-green-600 rounded-full' 
        : 'px-3 py-1 bg-blue-100 text-blue-600 rounded-full'
})

const statusIcon = computed(() => {
    return props.submission.grade !== null ? 'fas fa-check-circle' : 'fas fa-clock'
})

const gradeColorClass = computed(() => {
    const grade = props.submission.grade
    if (grade >= 90) return 'text-green-500'
    if (grade >= 80) return 'text-blue-500'
    if (grade >= 70) return 'text-yellow-500'
    if (grade >= 60) return 'text-orange-500'
    return 'text-red-500'
})

const gradeRemarks = computed(() => {
    const grade = props.submission.grade
    if (grade >= 90) return 'Excellent work! 🎉'
    if (grade >= 80) return 'Very good! 👍'
    if (grade >= 70) return 'Good job! 👏'
    if (grade >= 60) return 'Satisfactory ✅'
    return 'Needs improvement 📝'
})

const getDownloadRoute = computed(() => {
    return `/student/courses/${props.course.id}/assignments/${props.assignment.id}/submissions/${props.submission.id}/download`
})

const getResubmitRoute = computed(() => {
    return `/student/courses/${props.course.id}/assignments/${props.assignment.id}`
})

const getAssignmentRoute = computed(() => {
    return `/student/courses/${props.course.id}/assignments/${props.assignment.id}`
})

// Methods
const formatDateTime = (dateString) => {
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getFileName = (fileUrl) => {
    return fileUrl.split('/').pop() || 'Download File'
}
</script>
<template>
    <div class="min-h-screen bg-bg text-text transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 py-8">
        
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm mb-8">
                <a :href="assignmentsRoute" class="text-text hover:text-blue-500 flex items-center gap-1">
                    <i class="fas fa-arrow-left"></i>
                    Back to Assignments
                </a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <span class="text-blue-500 font-medium">{{ assignment.title }}</span>
            </nav>

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-500 to-purple-600 bg-clip-text text-transparent">
                        <span class="text-amber-50" >📋</span>  Assignment Details
                    </h2>
                    <p class="text-sm opacity-75 mt-1">
                        <strong>{{ course.title }}</strong> - {{ course.instructor.name }}
                    </p>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <a :href="submissionsRoute" 
                        class="px-6 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all font-semibold flex items-center gap-2">
                        <i class="fas fa-list-check"></i>
                        View Submissions ({{ assignment.submissions_count }})
                    </a>
                    
                    <a :href="editRoute" 
                        class="px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all font-semibold flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        Edit Assignment
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Assignment Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Assignment Info -->
                    <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-border">
                            <h3 class="text-xl font-semibold flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                Assignment Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                <label class="text-sm font-semibold text-gray-500">Title</label>
                                <p class="text-xl font-bold">{{ assignment.title }}</p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-semibold text-gray-500">Description</label>
                                    <p class="text-gray-600 dark:text-gray-400" v-if="assignment.description">
                                        {{ assignment.description }}
                                    </p>
                                    <p class="text-gray-400 italic" v-else>No description provided</p>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                    <label class="text-sm font-semibold text-gray-500">Due Date</label>
                                    <p class="text-lg" :class="dueDateClass">
                                        {{ formattedDueDate }}
                                        <span v-if="isOverdue" class="text-xs bg-red-500 text-white px-2 py-1 rounded ml-2">
                                            Overdue
                                        </span>
                                    </p>
                                    </div>
                                    
                                    <div>
                                        <label class="text-sm font-semibold text-gray-500">Created</label>
                                        <p class="text-lg">{{ formatDate(assignment.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Statistics -->
                    <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-border">
                            <h3 class="text-xl font-semibold flex items-center gap-2">
                                <i class="fas fa-chart-bar text-green-500"></i>
                                Submission Statistics
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-blue-500">{{ stats.total }}</div>
                                    <div class="text-sm text-gray-500">Total Submissions</div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-green-500">{{ stats.graded }}</div>
                                    <div class="text-sm text-gray-500">Graded</div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-orange-500">{{ stats.pending }}</div>
                                    <div class="text-sm text-gray-500">Pending</div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-purple-500">{{ completionRate }}%</div>
                                    <div class="text-sm text-gray-500">Completion Rate</div>
                                </div>
                            </div>

                            <!-- Progress Bars -->
                            <div class="mt-6 space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>Submission Progress</span>
                                        <span>{{ stats.total }} students</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" 
                                            :style="{ width: submissionProgress + '%' }"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>Grading Progress</span>
                                        <span>{{ stats.graded }}/{{ stats.total }} graded</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" 
                                            :style="{ width: gradingProgress + '%' }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Submissions -->
                    <div class="bg-card border border-border rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-border">
                            <h3 class="text-xl font-semibold flex items-center gap-2">
                                <i class="fas fa-clock text-orange-500"></i>
                                Recent Submissions
                                <span class="text-sm text-gray-500 font-normal">(Last 5 submissions)</span>
                            </h3>
                        </div>
                        
                        <div class="divide-y divide-border">
                            <div v-for="submission in recentSubmissions" :key="submission.id" 
                                class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ submission.student.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-text">{{ submission.student.name }}</div>
                                            <div class="text-xs text-gray-500">{{ submission.student.email }}</div>
                                        </div>
                                    </div>
                                
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm text-gray-500">{{ formatDateTime(submission.created_at) }}</span>
                                        
                                        <span :class="getSubmissionStatusClass(submission)" class="text-xs font-semibold">
                                            {{ getSubmissionStatusText(submission) }}
                                        </span>
                                        
                                        <a :href="getSubmissionRoute(submission)" 
                                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-xs flex items-center gap-1">
                                            <i class="fas fa-eye"></i>
                                            Grade
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="recentSubmissions.length === 0" class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                            <p>No submissions yet</p>
                        </div>

                        <div class="p-4 border-t border-border text-center">
                            <a :href="submissionsRoute" 
                                class="text-blue-500 hover:text-blue-600 font-semibold flex items-center justify-center gap-2">
                                <i class="fas fa-list"></i>
                                View All Submissions
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-card border border-border rounded-2xl p-6 shadow-lg">
                        <h3 class="font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-bolt text-yellow-500"></i>
                            Quick Actions
                        </h3>
                        <div class="space-y-3">
                            <a :href="submissionsRoute" 
                                class="w-full px-4 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all font-semibold flex items-center justify-center gap-2">
                                <i class="fas fa-play-circle"></i>
                                Start Grading
                            </a>
                            
                            <a :href="editRoute" 
                                class="w-full px-4 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all font-semibold flex items-center justify-center gap-2">
                                <i class="fas fa-cog"></i>
                                Edit Settings
                            </a>
                        
                            <button @click="deleteAssignment" 
                                    class="w-full px-4 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all font-semibold flex items-center justify-center gap-2">
                                <i class="fas fa-trash"></i>
                                Delete Assignment
                            </button>
                        </div>
                    </div>

                    <!-- Assignment Timeline -->
                    <div class="bg-card border border-border rounded-2xl p-6 shadow-lg">
                        <h3 class="font-semibold mb-4 flex items-center gap-2">
                        <i class="fas fa-history text-purple-500"></i>
                        Assignment Timeline
                        </h3>
                        <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-plus text-white text-xs"></i>
                            </div>
                            <div>
                            <p class="font-semibold">Assignment Created</p>
                            <p class="text-sm text-gray-500">{{ formatDateTime(assignment.created_at) }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-paper-plane text-white text-xs"></i>
                            </div>
                            <div>
                            <p class="font-semibold">First Submission</p>
                            <p class="text-sm text-gray-500">
                                {{ firstSubmissionDate || 'No submissions yet' }}
                            </p>
                            </div>
                        </div>
                        
                        <div v-if="assignment.due_date" class="flex items-center gap-3">
                            <div class="w-8 h-8" :class="dueDateIconClass">
                            <i class="fas fa-calendar text-white text-xs"></i>
                            </div>
                            <div>
                            <p class="font-semibold">Due Date</p>
                            <p class="text-sm" :class="dueDateTextClass">
                                {{ formattedDueDate }}
                            </p>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Performance Insights -->
                    <div class="bg-card border border-border rounded-2xl p-6 shadow-lg">
                        <h3 class="font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-chart-line text-orange-500"></i>
                            Performance Insights
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Average Grade</span>
                                <span class="font-semibold" :class="averageGradeColor">
                                    {{ averageGrade }}%
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Submission Rate</span>
                                <span class="font-semibold text-blue-500">{{ submissionProgress }}%</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">On-time Submissions</span>
                                <span class="font-semibold text-green-500">{{ onTimeRate }}%</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Late Submissions</span>
                                <span class="font-semibold text-red-500">{{ lateSubmissionsCount }}</span>
                            </div>
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
import axios from 'axios'

const props = defineProps({
    course: Object,
    assignment: Object,
    submissions: Object,
    submissionsRoute: String,
    editRoute: String,
    assignmentsRoute: String,
    csrfToken: String
})

const toast = useToast()

// Computed properties
const formattedDueDate = computed(() => {
    if (!props.assignment.due_date) return 'No due date'
    return new Date(props.assignment.due_date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
})

const isOverdue = computed(() => {
  if (!props.assignment.due_date) return false
  return new Date(props.assignment.due_date) < new Date()
})

const dueDateClass = computed(() => {
  if (!props.assignment.due_date) return 'text-gray-500'
  return isOverdue.value ? 'text-red-500' : 'text-green-500'
})

const dueDateIconClass = computed(() => {
  return isOverdue.value ? 'bg-red-500 rounded-full' : 'bg-green-500 rounded-full'
})

const dueDateTextClass = computed(() => {
  return isOverdue.value ? 'text-red-500' : 'text-green-500'
})

const stats = computed(() => {
    const total = props.assignment.submissions_count || 
                    props.assignment.submissions?.length || 
                    props.submissions?.data?.length || 
                    0
    
        const graded = props.assignment.graded_submissions_count || 
                    props.submissions?.data?.filter(s => s.grade !== null).length || 
                    0
    
    const pending = total - graded
    
    console.log('Debug Stats:', {
        assignmentSubmissionsCount: props.assignment.submissions_count,
        assignmentGradedCount: props.assignment.graded_submissions_count,
        submissionsDataLength: props.submissions?.data?.length,
        calculatedTotal: total,
        calculatedGraded: graded
    })
    
    return { total, graded, pending }
})

const completionRate = computed(() => {
    const enrolledStudents = props.course.enrollments_count || 
                            props.course.enrollments?.length || 
                            1
    
    console.log('Debug Enrollment:', {
        enrollmentsCount: props.course.enrollments_count,
        enrollmentsLength: props.course.enrollments?.length
    })
    
    return Math.round((stats.value.total / enrolledStudents) * 100)
})

const submissionProgress = computed(() => {
    const enrolledStudents = props.course.enrollments_count || 
                            props.course.enrollments?.length || 
                            1
    return Math.round((stats.value.total / enrolledStudents) * 100)
})

const gradingProgress = computed(() => {
    if (stats.value.total === 0) return 0
    return Math.round((stats.value.graded / stats.value.total) * 100)
})

const recentSubmissions = computed(() => {
    return props.submissions?.data?.slice(0, 5) || []
})

const firstSubmissionDate = computed(() => {
    if (!props.submissions?.data?.length) return null
    return formatDateTime(props.submissions.data[0].created_at)
})

const averageGrade = computed(() => {
    const submissions = props.submissions?.data || []
    const gradedSubmissions = submissions.filter(s => s.grade !== null)
    if (gradedSubmissions.length === 0) return 0
    
    const total = gradedSubmissions.reduce((sum, submission) => sum + submission.grade, 0)
    return Math.round(total / gradedSubmissions.length)
})

const onTimeRate = computed(() => {
    const submissions = props.submissions?.data || []
    if (submissions.length === 0) return 0
    const onTime = submissions.filter(s => !isSubmissionLate(s)).length
    return Math.round((onTime / submissions.length) * 100)
})

const lateSubmissionsCount = computed(() => {
    const submissions = props.submissions?.data || []
    return submissions.filter(s => isSubmissionLate(s)).length
})

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getSubmissionStatusText = (submission) => {
  return submission.grade !== null ? 'Graded' : 'Pending'
}

const getSubmissionStatusClass = (submission) => {
  return submission.grade !== null 
    ? 'px-2 py-1 bg-green-100 text-green-600 rounded-full text-xs' 
    : 'px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs'
}

const getSubmissionRoute = (submission) => {
    return `/instructor/courses/${props.course.id}/assignments/${props.assignment.id}/submissions/${submission.id}`
}

const isSubmissionLate = (submission) => {
    if (!props.assignment.due_date) return false
    return new Date(submission.created_at) > new Date(props.assignment.due_date)
}

const deleteAssignment = async () => {
    if (!confirm(`Delete assignment "${props.assignment.title}"? This will also delete all ${stats.value.total} submissions. This action cannot be undone.`)) return

    try {
        const response = await axios.delete(`/instructor/courses/${props.course.id}/assignments/${props.assignment.id}`, {
        headers: { 'X-CSRF-TOKEN': props.csrfToken }
        })

        if (response.data.success) {
        toast.success('Assignment deleted successfully!')
        // Redirect to assignments list
        setTimeout(() => {
            window.location.href = props.assignmentsRoute
        }, 1500)
        }
    } catch (err) {
        console.error('Delete error:', err)
        toast.error('Failed to delete assignment.')
    }
}

//////

</script>
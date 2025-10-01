<template>
  <div class="min-h-screen bg-bg text-text transition-colors duration-300">
    <div class="max-w-6xl mx-auto px-4 py-8">

      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm mb-8">
        <a :href="indexRoute" class="text-text hover:text-blue-500 flex items-center gap-1">
          <i class="fas fa-home"></i>
          Lessons
        </a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-blue-500 font-medium">{{ lesson.title }}</span>
      </nav>

      <!-- Access Control -->
      <div v-if="!hasAccess" class="mb-6">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center">
              <i class="fas fa-lock text-white text-xl"></i>
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-yellow-800 dark:text-yellow-300">Premium Content</h3>
              <p class="text-yellow-700 dark:text-yellow-400 text-sm mt-1">
                This lesson is part of our premium content. Enroll in the course to access all premium lessons.
              </p>
            </div>
            <a :href="enrollRoute" 
               class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all">
              Enroll Now
            </a>
          </div>
        </div>
      </div>

      <!-- Lesson Header -->
      <div class="bg-card border border-border rounded-2xl shadow-xl overflow-hidden mb-6">
        <div class="p-8">
          <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex-1">
              <h1 class="text-3xl font-bold mb-3">{{ lesson.title }}</h1>
              <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed">
                {{ lesson.description || 'No description provided.' }}
              </p>
              <div class="mt-4 flex flex-wrap gap-3">
                <span class="px-4 py-2 bg-blue-500 text-white rounded-xl text-sm font-semibold flex items-center gap-2">
                  <i class="fas fa-clock"></i>
                  {{ lesson.duration }} minutes
                </span>
                <span :class="[
                  'px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2',
                  lesson.is_free 
                    ? 'bg-green-500 text-white' 
                    : 'bg-purple-500 text-white'
                ]">
                  <i :class="lesson.is_free ? 'fas fa-unlock' : 'fas fa-crown'"></i>
                  {{ lesson.is_free ? 'Free Lesson' : 'Premium Lesson' }}
                </span>
                <span v-if="lesson.file_url" class="px-4 py-2 bg-gray-500 text-white rounded-xl text-sm font-semibold flex items-center gap-2">
                  <i class="fas fa-file"></i>
                  {{ getFileType(lesson.file_url) }}
                </span>
              </div>
            </div>
            
            <!-- Instructor Actions -->
            <div v-if="isInstructor" class="flex gap-3">
              <a :href="editRoute" 
                 class="px-6 py-3 bg-green-500 text-white rounded-xl shadow hover:shadow-lg transition-all flex items-center gap-2">
                <i class="fas fa-edit"></i>
                Edit Lesson
              </a>
              <a :href="indexRoute" 
                 class="px-6 py-3 border-2 border-border text-text rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Back to Lessons
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Video Player Section -->
      <div v-if="hasAccess && isVideoFile" class="mb-6">
        <div class="bg-card border border-border rounded-2xl shadow-xl overflow-hidden">
          <div class="p-6 border-b border-border">
            <h2 class="text-xl font-semibold flex items-center gap-2">
              <i class="fas fa-play-circle text-blue-500"></i>
              Video Content
            </h2>
          </div>
          <div class="p-6">
            <div class="aspect-w-16 aspect-h-9 bg-black rounded-xl overflow-hidden">
              <video 
                ref="videoPlayer"
                :src="lesson.file_url"
                controls
                controlsList="nodownload"
                class="w-full h-full object-cover"
                @contextmenu="preventRightClick"
              >
                Your browser does not support the video tag.
              </video>
            </div>
            
            <!-- Video Controls -->
            <div class="mt-4 flex flex-wrap gap-3 justify-between items-center">
              <div class="flex gap-3">
                <button @click="togglePlay" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all flex items-center gap-2">
                  <i :class="isPlaying ? 'fas fa-pause' : 'fas fa-play'"></i>
                  {{ isPlaying ? 'Pause' : 'Play' }}
                </button>
                <button @click="toggleMute" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all flex items-center gap-2">
                  <i :class="isMuted ? 'fas fa-volume-mute' : 'fas fa-volume-up'"></i>
                  {{ isMuted ? 'Unmute' : 'Mute' }}
                </button>
              </div>
              
              <div class="flex gap-3">
                <button @click="downloadFile" 
                        v-if="lesson.is_free"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all flex items-center gap-2">
                  <i class="fas fa-download"></i>
                  Download Video
                </button>
                <span v-else class="px-4 py-2 bg-purple-500 text-white rounded-lg flex items-center gap-2">
                  <i class="fas fa-crown"></i>
                  Download available for enrolled students
                </span>
              </div>
            </div>
            
            <!-- Progress Tracking -->
            <div v-if="isStudent" class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold">Your Progress</span>
                <span class="text-sm text-gray-500">{{ progressPercentage }}%</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                     :style="{ width: progressPercentage + '%' }"></div>
              </div>
              <div class="mt-2 flex justify-between text-xs text-gray-500">
                <span>Started</span>
                <span v-if="progressPercentage === 100" class="text-green-500 font-semibold">Completed!</span>
                <span v-else>In Progress</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Document Viewer -->
      <div v-else-if="hasAccess && isDocumentFile" class="mb-6">
        <div class="bg-card border border-border rounded-2xl shadow-xl overflow-hidden">
          <div class="p-6 border-b border-border">
            <h2 class="text-xl font-semibold flex items-center gap-2">
              <i class="fas fa-file-alt text-green-500"></i>
              Document Content
            </h2>
          </div>
          <div class="p-6">
            <div class="text-center py-8">
              <div class="w-20 h-20 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-pdf text-white text-2xl"></i>
              </div>
              <h3 class="text-lg font-semibold mb-2">{{ getFileName(lesson.file_url) }}</h3>
              <p class="text-gray-500 mb-4">Click below to view or download the document</p>
              
              <div class="flex gap-3 justify-center">
                <a :href="lesson.file_url" 
                   target="_blank"
                   class="px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all flex items-center gap-2">
                  <i class="fas fa-eye"></i>
                  View Document
                </a>
                <button @click="downloadFile" 
                        v-if="lesson.is_free"
                        class="px-6 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all flex items-center gap-2">
                  <i class="fas fa-download"></i>
                  Download
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Lesson Meta Information -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-card border border-border rounded-2xl p-6 shadow">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
              <i class="fas fa-calendar text-white"></i>
            </div>
            <h4 class="font-semibold">Created Date</h4>
          </div>
          <p class="text-lg">{{ formatDate(lesson.created_at) }}</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 shadow">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
              <i class="fas fa-sync text-white"></i>
            </div>
            <h4 class="font-semibold">Last Updated</h4>
          </div>
          <p class="text-lg">{{ formatDate(lesson.updated_at) }}</p>
        </div>
        
        <div class="bg-card border border-border rounded-2xl p-6 shadow">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
              <i class="fas fa-chart-line text-white"></i>
            </div>
            <h4 class="font-semibold">Access Type</h4>
          </div>
          <p class="text-lg">{{ lesson.is_free ? 'Free for all' : 'Premium content' }}</p>
        </div>
      </div>

      <!-- Navigation -->
      <div v-if="hasAccess" class="mt-8 flex justify-between">
        <button @click="previousLesson" 
                v-if="hasPreviousLesson"
                class="px-6 py-3 border-2 border-border text-text rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all flex items-center gap-2">
          <i class="fas fa-arrow-left"></i>
          Previous Lesson
        </button>
        <button @click="nextLesson" 
                v-if="hasNextLesson"
                class="px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all flex items-center gap-2 ml-auto">
          Next Lesson
          <i class="fas fa-arrow-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  lesson: Object,
  indexRoute: String,
  editRoute: String,
  enrollRoute: String,
  isInstructor: {
    type: Boolean,
    default: false
  },
  isStudent: {
    type: Boolean,
    default: false
  },
  hasAccess: {
    type: Boolean,
    default: false
  },
  courseLessons: {
    type: Array,
    default: () => []
  },
  currentLessonId: {
    type: Number,
    default: null
  }
})

// Refs
const videoPlayer = ref(null)
const isPlaying = ref(false)
const isMuted = ref(false)
const progressPercentage = ref(0)

// Computed properties
const isVideoFile = computed(() => {
  if (!props.lesson.file_url) return false
  const videoExtensions = ['.mp4', '.mov', '.avi', '.webm', '.mkv']
  return videoExtensions.some(ext => props.lesson.file_url.toLowerCase().endsWith(ext))
})

const isDocumentFile = computed(() => {
  if (!props.lesson.file_url) return false
  const docExtensions = ['.pdf', '.doc', '.docx', '.ppt', '.pptx']
  return docExtensions.some(ext => props.lesson.file_url.toLowerCase().endsWith(ext))
})

const hasPreviousLesson = computed(() => {
  if (!props.courseLessons.length) return false
  const currentIndex = props.courseLessons.findIndex(lesson => lesson.id === props.currentLessonId)
  return currentIndex > 0
})

const hasNextLesson = computed(() => {
  if (!props.courseLessons.length) return false
  const currentIndex = props.courseLessons.findIndex(lesson => lesson.id === props.currentLessonId)
  return currentIndex < props.courseLessons.length - 1
})

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getFileType = (fileUrl) => {
  const extension = fileUrl.split('.').pop().toLowerCase()
  const typeMap = {
    'mp4': 'Video',
    'mov': 'Video', 
    'avi': 'Video',
    'pdf': 'PDF Document',
    'doc': 'Word Document',
    'docx': 'Word Document',
    'ppt': 'Presentation',
    'pptx': 'Presentation'
  }
  return typeMap[extension] || 'File'
}

const getFileName = (fileUrl) => {
  return fileUrl.split('/').pop() || 'Download File'
}

const togglePlay = () => {
  if (!videoPlayer.value) return
  
  if (isPlaying.value) {
    videoPlayer.value.pause()
  } else {
    videoPlayer.value.play()
  }
  isPlaying.value = !isPlaying.value
}

const toggleMute = () => {
  if (!videoPlayer.value) return
  videoPlayer.value.muted = !videoPlayer.value.muted
  isMuted.value = videoPlayer.value.muted
}

const preventRightClick = (event) => {
  event.preventDefault()
}

const downloadFile = () => {
  if (!props.lesson.file_url) return
  
  const link = document.createElement('a')
  link.href = props.lesson.file_url
  link.download = getFileName(props.lesson.file_url)
  link.target = '_blank'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const previousLesson = () => {
  const currentIndex = props.courseLessons.findIndex(lesson => lesson.id === props.currentLessonId)
  if (currentIndex > 0) {
    const prevLesson = props.courseLessons[currentIndex - 1]
    window.location.href = `/courses/${prevLesson.course_id}/lessons/${prevLesson.id}`
  }
}

const nextLesson = () => {
  const currentIndex = props.courseLessons.findIndex(lesson => lesson.id === props.currentLessonId)
  if (currentIndex < props.courseLessons.length - 1) {
    const nextLesson = props.courseLessons[currentIndex + 1]
    window.location.href = `/courses/${nextLesson.course_id}/lessons/${nextLesson.id}`
  }
}

// Video event listeners
const handleTimeUpdate = () => {
  if (!videoPlayer.value) return
  
  const duration = videoPlayer.value.duration
  const currentTime = videoPlayer.value.currentTime
  
  if (duration > 0) {
    progressPercentage.value = Math.round((currentTime / duration) * 100)
    
    // Auto-mark as completed when 90% watched
    if (progressPercentage.value >= 90 && props.isStudent) {
      // Here you would typically send an API request to mark as completed
      console.log('Lesson completed!')
    }
  }
}

// Lifecycle
onMounted(() => {
  if (videoPlayer.value) {
    videoPlayer.value.addEventListener('timeupdate', handleTimeUpdate)
    videoPlayer.value.addEventListener('play', () => isPlaying.value = true)
    videoPlayer.value.addEventListener('pause', () => isPlaying.value = false)
  }
})

onUnmounted(() => {
  if (videoPlayer.value) {
    videoPlayer.value.removeEventListener('timeupdate', handleTimeUpdate)
    videoPlayer.value.removeEventListener('play', () => isPlaying.value = true)
    videoPlayer.value.removeEventListener('pause', () => isPlaying.value = false)
  }
})
</script>

<style scoped>
.aspect-w-16 {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
}

.aspect-w-16 > * {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
</style>
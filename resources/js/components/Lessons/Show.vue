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
            <div class="flex gap-3">
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
      <div v-if="isVideoFile" class="mb-6">
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
                class="w-full h-full object-cover"
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
                <button @click="downloadFile" 
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all flex items-center gap-2">
                  <i class="fas fa-download"></i>
                  Download Video
                </button>
              </div>
              
              <!-- Instructor Preview Info -->
              <div class="bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded-lg border border-blue-200">
                <span class="text-blue-700 dark:text-blue-300 text-sm font-semibold">
                  <i class="fas fa-eye mr-1"></i>
                  Instructor Preview Mode
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Document Viewer -->
      <div v-else-if="isDocumentFile" class="mb-6">
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
                        class="px-6 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all flex items-center gap-2">
                  <i class="fas fa-download"></i>
                  Download
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- No File Message -->
      <div v-else class="mb-6">
        <div class="bg-card border border-border rounded-2xl shadow-xl overflow-hidden">
          <div class="p-6 text-center">
            <div class="w-16 h-16 bg-gray-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <i class="fas fa-file text-white text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2">No File Attached</h3>
            <p class="text-gray-500 mb-4">This lesson doesn't have any content file yet.</p>
            <a :href="editRoute" 
               class="px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all flex items-center gap-2 inline-flex">
              <i class="fas fa-plus"></i>
              Add Content File
            </a>
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
      <div class="mt-8 flex justify-between">
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
    window.location.href = `/instructor/courses/${prevLesson.course_id}/lessons/${prevLesson.id}`
  }
}

const nextLesson = () => {
  const currentIndex = props.courseLessons.findIndex(lesson => lesson.id === props.currentLessonId)
  if (currentIndex < props.courseLessons.length - 1) {
    const nextLesson = props.courseLessons[currentIndex + 1]
    window.location.href = `/instructor/courses/${nextLesson.course_id}/lessons/${nextLesson.id}`
  }
}

// Video event listeners
const handlePlay = () => {
  isPlaying.value = true
}

const handlePause = () => {
  isPlaying.value = false
}

// Lifecycle
onMounted(() => {
  if (videoPlayer.value) {
    videoPlayer.value.addEventListener('play', handlePlay)
    videoPlayer.value.addEventListener('pause', handlePause)
  }
})

onUnmounted(() => {
  if (videoPlayer.value) {
    videoPlayer.value.removeEventListener('play', handlePlay)
    videoPlayer.value.removeEventListener('pause', handlePause)
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
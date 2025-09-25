<template>
    <div class="mt-8 flex justify-center">
        <div class="flex items-center gap-2 bg-card border border-border rounded-xl p-2 shadow-lg">
            <a 
                v-for="(link, index) in links" 
                :key="index"
                :href="link.url"
                class="px-4 py-2 rounded-lg transition-all duration-200 font-semibold min-w-[40px] text-center cursor-pointer"
                :class="[
                link.active 
                    ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg transform scale-105' 
                    : 'bg-bg text-text hover:bg-gray-100 dark:hover:bg-gray-800 border border-border hover:border-blue-300'
                ]"
                v-html="link.label"
            ></a>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({ links: Array })
</script>

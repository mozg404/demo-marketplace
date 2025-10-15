<script setup>
import { ref, watch, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const isLocked = ref(true)
const isLoading = ref(false)
const contentOpacity = ref(1)

const SETTINGS = {
  LOADER_DELAY: 150,
  MIN_LOAD_TIME: 500
}

let loadStartTime = 0

const startLoading = () => {
  loadStartTime = Date.now()
  isLoading.value = true
  isLocked.value = true
}

const finishLoading = () => {
  const elapsed = Date.now() - loadStartTime
  const remaining = Math.max(SETTINGS.MIN_LOAD_TIME - elapsed, 0)

  setTimeout(() => {
    isLocked.value = false
    isLoading.value = false
    contentOpacity.value = 1
  }, remaining)
}

// Надежное определение обычных переходов (не модалок)
const handleNavigation = () => {
  router.on('start', (event) => {
    // Если это НЕ модалка (preserveState: false или undefined)
    if (!event.detail.visit?.preserveState) {
      contentOpacity.value = 0
    }
  })
}

watch(() => router.isProcessing, (processing) => {
  processing ? startLoading() : finishLoading()
})

onMounted(() => {
  handleNavigation()
  startLoading()
  setTimeout(finishLoading, SETTINGS.MIN_LOAD_TIME)
})
</script>

<template>
  <!-- Лоадер -->
  <Transition name="loader-fade">
    <div
      v-if="isLoading"
      class="fixed inset-0 bg-white dark:bg-gray-900 z-50 flex items-center justify-center"
    >
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>
  </Transition>

  <!-- Контент -->
  <div :class="{ 'opacity-0': isLocked, 'opacity-100': !isLocked }">
    <div
      class="transition-opacity duration-300 ease-in-out"
      :style="{ opacity: contentOpacity }"
    >
      <slot />
    </div>
  </div>
</template>

<style scoped>
/* Анимация лоадера */
.loader-fade-enter-active,
.loader-fade-leave-active {
  transition: opacity 200ms ease;
}
.loader-fade-enter-from,
.loader-fade-leave-to {
  opacity: 0;
}

/* Управление видимостью */
.opacity-0 {
  opacity: 0;
  pointer-events: none;
  height: 100vh;
  overflow: hidden;
}
.opacity-100 {
  transition: opacity 300ms ease-out;
}
</style>
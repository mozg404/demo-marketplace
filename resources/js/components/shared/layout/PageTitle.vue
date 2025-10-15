<script setup>
import { cn } from '@/lib/utils.ts'

const props = defineProps({
  class: { type: String, default: '' }
})
</script>

<template>
  <div :class="cn('text-4xl font-semibold tracking-tight', props.class)">
    <slot/>
  </div>
</template>

<style scoped>

</style>
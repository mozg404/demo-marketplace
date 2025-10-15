<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
  class: { type: String, default: '' }
})
</script>

<template>
  <div :class="cn('flex justify-between items-center', props.class)">
    <slot />
  </div>
</template>
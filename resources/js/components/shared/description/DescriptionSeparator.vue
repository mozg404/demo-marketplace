<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
  class: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <span :class="cn('flex-1 border-b border-dashed border-border mx-2 relative top-[5px]', props.class)"></span>
</template>
<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
  class: { type: String, default: '' }
})
</script>

<template>
  <dt :class="cn('text-muted-foreground', props.class)">
    <slot />
  </dt>
</template>
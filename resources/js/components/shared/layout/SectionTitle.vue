<script setup >
import { cn } from '@/lib/utils'

const props = defineProps({
  class: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <section :class="cn('text-2xl font-semibold mb-4', props.class)">
    <slot/>
  </section>
</template>

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
  <section :class="cn('mb-12', props.class)">
    <slot/>
  </section>
</template>

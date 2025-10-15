<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
  class: { type: String, default: '' }
})
</script>

<template>
  <dd :class="cn('text-right', props.class)">
    <slot />
  </dd>
</template>
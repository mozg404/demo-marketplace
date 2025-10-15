<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
  class: { type: null, required: false },
});
</script>

<template>
  <tr
    data-slot="table-row"
    :class="
      cn(
        'data-[state=selected]:bg-muted border-b transition-colors',
        props.class,
      )
    "
  >
    <slot />
  </tr>
</template>

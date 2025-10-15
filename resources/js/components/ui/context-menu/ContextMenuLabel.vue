<script setup>
import { cn } from '@/lib/utils';
import { ContextMenuLabel } from 'reka-ui';
import { computed } from 'vue';

const props = defineProps({
  asChild: { type: Boolean, required: false },
  as: { type: null, required: false },
  class: { type: null, required: false },
  inset: { type: Boolean, required: false },
});

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});
</script>

<template>
  <ContextMenuLabel
    data-slot="context-menu-label"
    :data-inset="inset ? '' : undefined"
    v-bind="delegatedProps"
    :class="
      cn(
        'text-foreground px-2 py-1.5 text-sm font-medium data-[inset]:pl-8',
        props.class,
      )
    "
  >
    <slot />
  </ContextMenuLabel>
</template>

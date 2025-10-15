<script setup>
import { cn } from '@/lib/utils';
import { ComboboxViewport, useForwardProps } from 'reka-ui';
import { computed } from 'vue';

const props = defineProps({
  nonce: { type: String, required: false },
  asChild: { type: Boolean, required: false },
  as: { type: null, required: false },
  class: { type: null, required: false },
});

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});

const forwarded = useForwardProps(delegatedProps);
</script>

<template>
  <ComboboxViewport
    data-slot="combobox-viewport"
    v-bind="forwarded"
    :class="
      cn(
        'max-h-[300px] scroll-py-1 overflow-x-hidden overflow-y-auto',
        props.class,
      )
    "
  >
    <slot />
  </ComboboxViewport>
</template>

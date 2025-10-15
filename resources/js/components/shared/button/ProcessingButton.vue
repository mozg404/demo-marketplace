<script setup>
import {LoaderCircle} from "lucide-vue-next";
import {Button} from "@/components/ui/button/index.js";
import {cn} from "@/lib/utils.js";

const props = defineProps({
  isLoading: {
    type: Boolean,
    required: true,
  },
  type: {
    type: String,
    default: 'submit',
  },
  variant: {
    type: String,
    default: 'default',
  },
  class: {
    type: String,
  }
})
</script>

<template>
  <Button :type="type" :variant="variant" :disabled="isLoading" :class="cn('cursor-pointer', props.class)">
    <LoaderCircle v-if="isLoading" class="h-4 w-4 animate-spin"/>
    <slot/>
  </Button>
</template>

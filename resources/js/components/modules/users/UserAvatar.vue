<script setup>
import {ImageOff} from 'lucide-vue-next'
import {AspectRatio} from "@components/ui/aspect-ratio/index.js";
import {cn} from "@/lib/utils.js";

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  conversion: {
    type: String,
    default: 'medium',
  },
  class: {
    type: String,
  }
})
</script>

<template>
  <AspectRatio :ratio="1" :class="cn('bg-muted rounded-full overflow-hidden relative', props.class)">
    <img
      v-if="user?.avatar"
      :src="user?.avatar[conversion]"
      :alt="user?.name ?? 'Аватар'"
      class="object-cover rounded-md w-full h-full transition-transform duration-300 ease-in-out hover:scale-105"
    />
    <div v-else class="flex items-center justify-center h-full text-muted-foreground rounded-md">
      <ImageOff class="h-6 w-6" />
    </div>
  </AspectRatio>
</template>
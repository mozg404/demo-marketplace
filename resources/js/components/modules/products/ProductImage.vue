<script setup>
import { AspectRatio } from '@/components/ui/aspect-ratio/index.js';
import { ImageOff } from 'lucide-vue-next';
import {cn} from "@/lib/utils.js";

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  conversion: {
    type: String,
    default: 'medium',
  },
  class: {
    type: String,
  },
});
</script>

<template>
  <AspectRatio :ratio="3 / 4" :class="cn('bg-muted rounded-md overflow-hidden relative', props.class)">
    <img
      v-if="product?.preview?.[conversion] ?? false"
      :src="product?.preview[conversion]"
      :alt="product?.name ?? 'Изображение товара'"
      class="object-cover rounded-md w-full h-full transition-transform duration-300 ease-in-out hover:scale-105"
    />
    <div v-else class="flex items-center justify-center h-full text-muted-foreground rounded-md">
      <ImageOff class="h-6 w-6" />
    </div>
  </AspectRatio>
</template>
<script setup>
import { ChevronDown, ChevronUp } from 'lucide-vue-next';
import {onMounted, ref} from "vue";
import autoAnimate from '@formkit/auto-animate'
import {Link} from "@inertiajs/vue3";
import {cn} from "@/lib/utils.ts";
import {Button} from "@/components/ui/button/index.js";

const props = defineProps({
  category: Object,
})

const shouldExpandCategory = (category) => {
  // Проверяем текущую категорию
  if (route().current('catalog.category', { full_path: category.full_path })) {
    return true;
  }

  // Рекурсивно проверяем детей
  if (category.children && category.children.length > 0) {
    return category.children.some(child => shouldExpandCategory(child));
  }

  return false;
};

const isOpen = ref(shouldExpandCategory(props.category));
const isActive = shouldExpandCategory(props.category);
const contentRef = ref()

onMounted(() => {
  autoAnimate(contentRef.value) // Инициализация анимации
})
</script>

<template>
  <div :class="cn('flex items-center justify-between -mx-[1rem] my-1 whitespace-nowrap rounded-md text-sm',
      'font-medium transition-all outline-none hover:bg-accent hover:text-accent-foreground h-9 px-4  cursor-pointer',
      isActive ? 'bg-accent text-accent-foreground' : ''
    )">
    <Link :href="route('catalog.category', category.full_path)" class="flex-1 py-2 h-full">{{ category.name }}</Link>
    <button size="icon" class="p-1 -mr-[0.5rem] hover:text-accent-foreground cursor-pointer hover:opacity-80 transition-opacity" variant="ghost" @click="isOpen = !isOpen">
      <ChevronDown class="h-5 w-5" v-if="!isOpen" />
      <ChevronUp class="h-5 w-5" v-else />
    </button>
  </div>

  <div ref="contentRef">
    <div v-if="isOpen">
      <slot />
    </div>
  </div>
</template>
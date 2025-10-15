<script setup>
import { Link } from '@inertiajs/vue3'
import {cn} from "@/lib/utils.ts";
import {computed} from "vue";
import {Button} from "@/components/ui/button/index.js";

const props = defineProps({
  pagination: Object,
  class: {
    type: String,
    default: ''
  }
})

// Обрабатываем ссылки, добавляя тип
const processedLinks = computed(() => {
  return props.pagination.links.map(link => {
    if (link.label.includes('&laquo;') || link.label.includes('Назад')) {
      return { ...link, type: 'first' };
    }
    if (link.label.includes('&raquo;') || link.label.includes('Вперед')) {
      return { ...link, type: 'last' };
    }
    if (link.label === '...') {
      return { ...link, type: 'separator' };
    }
    return { ...link, type: 'page' };
  });
});
</script>

<template>
  <nav v-if="pagination.last_page > 1" :class="cn('relative flex space-x-2', props.class)">
    <template v-for="link in processedLinks" :key="link.label">
      <template v-if="link.type === 'page'">
        <Button :variant="link.active ? 'default' : 'outline'" size="icon" :disabled="true" class="rounded-full" as-child>
          <Link :disabled="true" :href="link.url ?? ''">{{link.label}}</Link>
        </Button>
      </template>
      <template v-if="link.type === 'separator'">
        <div class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md font-medium size-9 text-lg py-4" size="icon">...</div>
      </template>
    </template>
  </nav>
</template>
<script setup>
import { cn } from '@/lib/utils' // Предполагаем, что у вас есть функция cn()

const props = defineProps({
  class: {
    type: String,
    default: ''
  },
  contentClass: {
    type: String,
    default: ''
  },
  // Опциональные пропсы для отдельных сайдбаров
  leftClass: {
    type: String,
    default: ''
  },
  rightClass: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <div class="flex w-full">
    <!-- Левый сайдбар -->
    <aside
      v-if="$slots.sidebar_left"
      :class="cn('w-64 flex-none min-w-[20rem] space-y-8', 'pr-8', props.class, leftClass)"
    >
      <slot name="sidebar_left"/>
    </aside>

    <!-- Основной контент -->
    <main :class="cn('flex-1', contentClass, $attrs.contentClass)">
      <slot/>
    </main>

    <!-- Правый сайдбар -->
    <aside
      v-if="$slots.sidebar_right"
      :class="cn('w-64 flex-none min-w-[20rem]', 'pl-8', props.class, rightClass)"
    >
      <slot name="sidebar_right"/>
    </aside>
  </div>
</template>
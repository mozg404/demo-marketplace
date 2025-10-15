<script setup>
import { computed, useAttrs, provide } from 'vue'
import ErrorMessage from "@/components/shared/ErrorMessage.vue";
import FormLabel from "@/components/shared/form/FormLabel.vue";
import { cn } from '@/lib/utils'

const props = defineProps({
  label: {
    type: String,
    default: null
  },
  error: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  class: {
    type: String,
    default: ''
  },
  name: String // Новый пропс для связи
});

// Генерируем или используем переданный ID
const fieldId = computed(() =>
  props.name || useAttrs().id || `field-${Math.random().toString(36).slice(2, 9)}`
)

// Передаём ID в дочерние компоненты через provide
provide('fieldId', fieldId)
</script>

<template>
  <div :class="cn('grid gap-2', props.class)">
    <FormLabel v-if="label" :for="fieldId">{{ label }} <span v-if="required" class="text-red-600">*</span></FormLabel>
    <slot :id="fieldId" />
    <ErrorMessage v-if="error" :message="error"/>
  </div>
</template>
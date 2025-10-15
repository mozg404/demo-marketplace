<script>
export default {
  inheritAttrs: false
}
</script>

<script setup>
import { Switch } from '@/components/ui/switch'
import FormLabel from '@/components/shared/form/FormLabel.vue'
import { computed, useAttrs, ref, watch } from 'vue'
import {cn} from "@/lib/utils.js";

const props = defineProps({
  modelValue: [Boolean, String],
  label: String,
  required: Boolean,
  id: String,
  class: {
    type: String,
  }
})
const emit = defineEmits(['update:modelValue'])
const attrs = useAttrs()
const localId = computed(() => props.id || attrs.id || `switch-${Math.random().toString(36).slice(2, 5)}`)

// Функция преобразования
const convertToBoolean = (value) => {
  if (typeof value === 'boolean') return value
  return ['true', 'on', '1'].includes(String(value).toLowerCase())
}

// Мгновенно преобразуем строку в boolean при получении
watch(
  () => props.modelValue,
  (newVal) => {
    if (typeof newVal === 'string') {
      emit('update:modelValue', convertToBoolean(newVal))
    }
  },
  { immediate: true } // Сработает при первом рендере
)

const value = computed({
  get: () => convertToBoolean(props.modelValue),
  set: (val) => emit('update:modelValue', val)
})
</script>

<template>
  <div :class="cn('flex items-center gap-3', props.class)">
    <Switch
      v-model="value"
      :id="localId"
      class="cursor-pointer"
    />
    <FormLabel
      v-if="label"
      :for="localId"
      class="cursor-pointer"
    >
      {{ label }}
      <span v-if="required" class="text-red-600">*</span>
    </FormLabel>
  </div>
</template>
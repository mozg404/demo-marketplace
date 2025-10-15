<script setup>
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import {cn} from "@/lib/utils.js";
import { inject, computed } from 'vue'

const props = defineProps({
  modelValue: [String, Number, Boolean, Object],
  id: String,
  options: {
    type: Array,
    required: true
  },
  valueKey: {
    type: String,
    default: 'id'
  },
  labelKey: {
    type: String,
    default: 'name'
  },
  placeholder: {
    type: String,
    default: 'Выберите вариант'
  },
  emptyOption: {
    type: Boolean,
    default: false
  },
  class: {
    type: String,
  }
})
const parentId = inject('fieldId', null)
const finalId = computed(() => props.id || parentId?.value || undefined)
const emit = defineEmits(['update:modelValue'])
const handleChange = (value) => {
  emit('update:modelValue', value)
}
</script>

<template>
  <Select
    :disablePortal="true"
    :model-value="modelValue"
    @update:model-value="handleChange"
  >
    <SelectTrigger :class="cn('w-full cursor-pointer', props.class)" :id="finalId" v-bind="$attrs">
      <SelectValue :placeholder="placeholder" />
    </SelectTrigger>
    <SelectContent
      position="popper"
      :teleport="true"
      positionStrategy="fixed"
      class="z-[1000]"
    >
      <SelectItem v-if="emptyOption" :value="null">{{placeholder}}</SelectItem>
      <SelectItem
        v-for="(option, index) in options"
        :key="index"
        :value="option[valueKey]"
      >
        {{ option[labelKey] }}
      </SelectItem>
    </SelectContent>
  </Select>
</template>
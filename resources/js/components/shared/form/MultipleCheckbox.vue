<script setup>
import {Checkbox} from '@/components/ui/checkbox'
import {Label} from '@/components/ui/label'
import {computed,} from 'vue'
import {cn} from "@/lib/utils.js";
import {generateUniqueId} from "@/lib/support.js";

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  value: {
    type: String,
    required: true
  },
  label: String,
  class: {
    type: String,
  },
  labelClass: {
    type: String,
  },
  labelActiveClass: {
    type: String,
    default: "",
  },
})

const emit = defineEmits(['update:modelValue'])
const localId = computed(() => generateUniqueId('checkbox'))
const internalValue = computed({
  get: () => props.modelValue.includes(props.value),
  set: (val) => {
    const newArr = val
      ? [...props.modelValue, props.value]
      : props.modelValue.filter(v => v !== props.value);
    emit('update:modelValue', newArr);
  }
});

const getLabelClasses = () => internalValue.value ? props.labelActiveClass : ''

</script>

<template>
  <div :class="cn('flex items-center space-x-2', props.class)">
    <Checkbox
      v-model="internalValue"
      :id="localId"
      class="cursor-pointer"
    />
    <Label
      v-if="label"
      :for="localId"
      :class="cn(props.labelClass, getLabelClasses())"
    >{{ label }}</Label>
  </div>
</template>
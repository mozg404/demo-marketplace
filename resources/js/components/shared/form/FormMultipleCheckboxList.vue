<script>
export default {
  inheritAttrs: false
}
</script>

<script setup>
import MultipleCheckbox from "@/components/shared/form/MultipleCheckbox.vue";
import {computed} from "vue";

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
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
  class: {
    type: String,
    default: '',
  }
})

const emit = defineEmits(['update:modelValue'])
const checkedValues = computed({
  get: () => props.modelValue,
  set: (newValue) => emit('update:modelValue', newValue)
})
</script>

<template>
  <MultipleCheckbox
    v-for="option in options"
    :key="option[valueKey]"
    :value="option[valueKey]"
    :label="option[labelKey]"
    v-model="checkedValues"
    :class="props.class"
  />
</template>
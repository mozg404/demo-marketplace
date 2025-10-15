<script setup>

import {Input} from "@/components/ui/input";
import {Search} from "lucide-vue-next";

const props = defineProps({
  modelValue: {
    type: [String, Number],
    required: false
  },
  placeholder: {
    type: String,
    required: false,
    default: "Поиск"
  },
  id: {
    type: String,
    required: false,
    default: "search"
  },
})

const emit = defineEmits(['update:modelValue'])
const updateValue = (event) => {
  emit('update:modelValue', event.target.value);
};
</script>

<template>
  <div class="relative w-full items-center">
    <Input
      :id="id"
      type="text"
      :placeholder="placeholder"
      class="pl-8"
      :default-value="modelValue"
      @input="updateValue"
    />
    <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
      <Search class="size-4 text-muted-foreground"/>
    </span>
  </div>
</template>
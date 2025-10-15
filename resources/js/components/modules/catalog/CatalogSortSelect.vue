<script setup>
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
  modelValue: [String, Number, null],
})

const emit = defineEmits(['update:modelValue'])
const handleChange = (value) => {
  emit('update:modelValue', value)
}
</script>

<template>
  <Select
    :model-value="modelValue"
    @update:model-value="handleChange"
  >
    <SelectTrigger class="w-[200px]">
      <SelectValue placeholder="Выбрать" />
    </SelectTrigger>
    <SelectContent>
      <SelectGroup>
        <SelectItem value="rating">По рейтингу</SelectItem>
        <SelectItem value="latest">Сначала новые</SelectItem>
        <SelectItem value="oldest">Сначала старые</SelectItem>
        <SelectItem value="price_desc">Сначала дорогие</SelectItem>
        <SelectItem value="price_asc">Сначала дешевые</SelectItem>
      </SelectGroup>
    </SelectContent>
  </Select>
</template>

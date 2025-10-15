<script setup>
import { CommandItem } from '@/components/ui/command/index.js'
import { CornerDownRight } from 'lucide-vue-next';

const props = defineProps({
  categories: {
    type: Array,
    required: true,
    default: () => []
  },
  searchQuery: {
    type: String,
    default: ''
  },
  level: {
    type: Number,
    default: 0
  },
  selectedId: {  // Добавляем новый пропс
    type: [String, Number],
    default: null
  }
})

const emit = defineEmits(['select'])

// Проверяет, соответствует ли категория поиску или её дети
const isCategoryVisible = (category) => {
  if (!props.searchQuery) return true

  const nameMatches = category.name.toLowerCase().includes(props.searchQuery.toLowerCase())
  if (nameMatches) return true

  if (category.children?.length) {
    return category.children.some(child => isCategoryVisible(child))
  }

  return false
}

// Проверяет, является ли категория выбранной
const isSelected = (categoryId) => {
  return categoryId === props.selectedId
}
</script>

<template>
  <template v-for="category in categories" :key="category?.id">
    <template v-if="isCategoryVisible(category)">
      <CommandItem
        v-if="category?.id"
        :value="category.name"
        @select="() => emit('select', category)"
        :style="{ paddingLeft: `${level * 21 + 12}px` }"
        :class="{
          'bg-accent text-accent-foreground': isSelected(category.id)
        }"
      >
        <CornerDownRight v-if="level > 0" />
        {{ category.name }}
      </CommandItem>

      <CategoryItems
        v-if="category?.children?.length"
        :categories="category.children"
        :search-query="searchQuery"
        :level="level + 1"
        :selected-id="selectedId"
        @select="emit('select', $event)"
      />
    </template>
  </template>
</template>
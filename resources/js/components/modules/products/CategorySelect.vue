<script setup>
import { ref, watch, computed } from 'vue'
import { Button } from '@/components/ui/button/index.js'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command/index.js'
import { ChevronsUpDown } from 'lucide-vue-next'
import CategoryItems from "@/components/modules/products/CategoryItems.vue";

const props = defineProps({
  categories: {
    type: Array,
    required: true,
    default: () => []
  },
  modelValue: {
    type: [String, Number],
    default: null
  }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const searchQuery = ref('')
const selectedCategory = ref(null)

// Рекурсивный поиск категории по ID
const findCategoryById = (categories, id) => {
  for (const category of categories) {
    if (category?.id === id) {
      return category
    }
    if (category?.children?.length) {
      const found = findCategoryById(category.children, id)
      if (found) return found
    }
  }
  return null
}

// Инициализация и отслеживание изменений значения
watch(() => props.modelValue, (newVal) => {
  if (!newVal) {
    selectedCategory.value = null
    return
  }

  selectedCategory.value = findCategoryById(props.categories, newVal)
}, { immediate: true })

const handleSelect = (category) => {
  if (!category?.id) return

  selectedCategory.value = category
  emit('update:modelValue', category.id)
  isOpen.value = false
  searchQuery.value = '' // Сбрасываем поиск при выборе
}

// Сбрасываем поиск при открытии/закрытии
watch(isOpen, (newVal) => {
  if (newVal) {
    searchQuery.value = ''
  }
})
</script>

<template>
  <div class="relative">
    <Button
      type="button"
      variant="outline"
      class="w-full justify-between"
      @click="isOpen = !isOpen"
    >
      {{ selectedCategory?.name || "Выберите категорию" }}
      <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
    </Button>

    <div
      v-if="isOpen"
      class="absolute top-full left-0 mt-1 w-full z-50 bg-background border rounded-md shadow-lg"
    >
      <Command>
        <CommandInput
          v-model="searchQuery"
          placeholder="Поиск категории..."
          class="border-b"
        />
        <CommandEmpty>Категория не найдена</CommandEmpty>
        <CommandGroup>
          <CommandList>
            <CategoryItems
              :categories="categories"
              :search-query="searchQuery"
              :level="0"
              :selected-id="modelValue"
              @select="handleSelect"
            />
          </CommandList>
        </CommandGroup>
      </Command>
    </div>
  </div>
</template>
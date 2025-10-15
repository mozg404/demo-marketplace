<script>
export default {
  inheritAttrs: false // Отключаем автоматическое применение атрибутов
}
</script>

<script setup>
import { inject, computed } from 'vue'
import { Input } from '@/components/ui/input'

const props = defineProps({
  id: String,
  modelValue: [String, Number],
})

// Получаем ID из родительского FormField (если есть)
const parentId = inject('fieldId', null)

// Финальный ID: слот > пропс > инжект
const finalId = computed(() => props.id || parentId?.value || undefined)

const emit = defineEmits(['update:modelValue'])

const handleInput = (e) => {
  emit('update:modelValue', e.target.value.replace(/[^0-9]/g, ''))
}

const handleKeyDown = (e) => {
  // Разрешаем:
  // 1. Цифры (0-9)
  // 2. Служебные клавиши (Backspace, Delete, Tab, стрелки)
  // 3. Комбинации с Ctrl (Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X)
  // 4. Клавиши-модификаторы (Shift, Alt, Meta)
  if (
    !/[0-9]/.test(e.key) &&
    !['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key) &&
    !(e.ctrlKey || e.metaKey) && // Разрешаем Ctrl/Cmd + что угодно
    !e.altKey // Разрешаем Alt (может быть нужно для переключения раскладки)
  ) {
    e.preventDefault()
  }
}

const handlePaste = (e) => {
  e.preventDefault()
  const pastedText = e.clipboardData?.getData('text/plain') || ''
  const numbersOnly = pastedText.replace(/[^0-9]/g, '')
  document.execCommand('insertText', false, numbersOnly)
}
</script>

<template>
  <Input
    :id="finalId"
    :model-value="modelValue"
    @input="handleInput"
    @keydown="handleKeyDown"
    @paste="handlePaste"
    v-bind="$attrs"
    type="text"
    inputmode="decimal"
  />
</template>
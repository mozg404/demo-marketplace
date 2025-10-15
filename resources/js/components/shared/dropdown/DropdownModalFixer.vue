<script setup>
/**
 * FIX обертка для работы DropdownModalLink
 * Вшил его в оригинальный компонент, но этот оставим, вдруг перепишу случайно
 */
import { ref, provide } from 'vue'

const dropdownOpen = ref(false)
provide('dropdownOpen', dropdownOpen)
</script>

<template>
  <slot />
</template>
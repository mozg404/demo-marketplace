<script setup>
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar/index.js'
import { User2Icon } from 'lucide-vue-next'

const props = defineProps({
  user: {
    type: Object,
    default: null
  },
  size: {
    type: String,
    default: 'md',
    validator: (val) => ['sm', 'md', 'lg'].includes(val)
  }
})

// Размеры как у кнопок shadcn
const sizeClasses = {
  sm: 'h-8 w-8',
  md: 'h-9 w-9',
  lg: 'h-11 w-11'
}

// Размер иконки
const iconSize = {
  sm: 16,
  md: 18,
  lg: 20
}
</script>

<template>
  <Avatar :class="['bg-gray-100', sizeClasses[size]]">
    <AvatarImage
      v-if="user?.avatar"
      :src="user?.avatar?.thumb"
      :alt="'User avatar'"
      class="object-cover"
    />
    <AvatarFallback
      v-else
      class="text-gray-500 bg-gray-100"
    >
      <User2Icon :size="iconSize[size]" />
    </AvatarFallback>
  </Avatar>
</template>
<script setup>
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog/index.js'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  route: {
    type: String,
    required: true
  },
  method: {
    type: String,
    default: 'delete',
  },
  title: {
    type: String,
    default: 'Вы уверены?'
  },
  description: {
    type: String,
    default: 'Это действие нельзя отменить. Это навсегда удалит запись.'
  },
  cancelText: {
    type: String,
    default: 'Отмена'
  },
  confirmText: {
    type: String,
    default: 'Удалить'
  }
})

const form = useForm({})
</script>

<template>
  <AlertDialog>
    <AlertDialogTrigger as-child>
      <slot />
    </AlertDialogTrigger>
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>{{ title }}</AlertDialogTitle>
        <AlertDialogDescription>
          {{ description }}
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel>{{ cancelText }}</AlertDialogCancel>
        <AlertDialogAction @click="form.submit(method, route)" :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
          {{ confirmText }}
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

<style scoped>
</style>
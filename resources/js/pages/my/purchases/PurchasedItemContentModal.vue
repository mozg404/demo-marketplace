<script setup>
import { Modal } from '@inertiaui/modal-vue'
import { Separator } from "@/components/ui/separator"

const props = defineProps({
  purchasedItem: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <Modal>
    <div class="text-2xl font-semibold mb-8">{{ purchasedItem.name }}</div>

    <div>
      <div class="text-muted-foreground text-sm">Ключ активации:</div>
      <div class="font-semibold text-xl">{{ purchasedItem.content }}</div>
    </div>

    <Separator class="my-6" />

    <div class="text-lg font-semibold mb-2">Инструкция по активации:</div>
    <div class="prose prose-sm max-w-full" v-html="purchasedItem.instruction"></div>
  </Modal>
</template>
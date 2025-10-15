<script setup>
import {Badge} from "@/components/ui/badge/index.js";

defineProps({
  product: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <Badge v-if="product.status === 'active' && product.available_stock_items_count > 0">Продается</Badge>
  <Badge v-else-if="product.status === 'active' && product.available_stock_items_count === 0" variant="destructive">Закончился</Badge>
  <Badge v-else-if="product.status === 'draft'" variant="secondary">Черновик</Badge>
  <Badge v-else-if="product.status === 'paused'" variant="secondary">На паузе</Badge>
  <Badge v-else variant="destructive">Неизвестный статус</Badge>
</template>
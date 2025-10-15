<script setup>
import {Badge} from "@/components/ui/badge/index.js";

defineProps({
  status: String,
});
</script>

<template>
  <Badge v-if="status === 'completed'">Оплачен</Badge>
  <Badge v-else-if="status === 'pending'" variant="destructive">Ожидает оплаты</Badge>
  <Badge v-else-if="status === 'cancelled'" variant="secondary">Отменен</Badge>
  <Badge v-else variant="destructive">НЕИЗВЕСТНО</Badge>
</template>
<script setup>
import { computed } from 'vue';

const props = defineProps({
  value: {
    type: [Number, String],
    required: true
  },
  withoutCurrency: {
    type: Boolean,
    default: false
  },
  currency: {
    type: String,
    default: '₽'
  }
});

const formattedPrice = computed(() => {
  const num = typeof props.value === 'string'
    ? parseFloat(props.value.replace(/\s/g, ''))
    : props.value;

  return new Intl.NumberFormat('ru-RU').format(num);
});
</script>

<template>
  <span>{{ formattedPrice }} <template v-if="!withoutCurrency">{{currency}}</template></span>
</template>
<script setup>
import dayjs from '@/lib/dayjs.js';
import relativeTime from 'dayjs/plugin/relativeTime';
import { computed } from 'vue';

dayjs.extend(relativeTime);

const props = defineProps({
  registrationDate: {
    type: [String, Date, Number],
    required: true,
    validator: (value) => {
      try {
        return dayjs(value).isValid();
      } catch {
        return false;
      }
    }
  }
});

const accountAge = computed(() => {
  try {
    const regDate = dayjs(props.registrationDate);
    if (!regDate.isValid()) return 'Некорректная дата';

    return regDate.fromNow(true);
  } catch (error) {
    console.error('Ошибка форматирования даты:', error);
    return '—';
  }
});
</script>

<template>
  <span>{{ accountAge }}</span>
</template>
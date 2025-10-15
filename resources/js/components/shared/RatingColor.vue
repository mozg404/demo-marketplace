<script setup>
  const props = defineProps({
    rating: {
      type: Number,
      required: true,
    }
  })
</script>

<template>
  <span v-if="rating < 50" class="text-destructive">{{ rating }}</span>
  <span v-else class="text-green-800 hover:text-green-800">{{ rating }}</span>
</template>
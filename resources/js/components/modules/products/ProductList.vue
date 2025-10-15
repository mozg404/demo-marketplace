<script setup>
import ProductCard from "@/components/modules/products/ProductCard.vue";

const props = defineProps({
  products: Array,
})
</script>

<template>
  <div class="mt-6 grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 xl:gap-x-8">
    <ProductCard v-for="product in products" :key="product.id" :product="product" />
  </div>
</template>

<style scoped>

</style>
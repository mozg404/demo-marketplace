<script setup>
import ProductCard from "@/components/modules/products/ProductCard.vue";
import ProductListEmpty from "@/components/modules/products/ProductListEmpty.vue";
import {cn} from "@/lib/utils.js";

const props = defineProps({
  products: Array,
  class: {
    type: String,
    default: "",
  }
})
</script>

<template>
  <div v-if="products.length > 0" :class="cn('grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8 w-full', props.class)">
    <ProductCard v-for="product in products" :key="product.id" :product="product" />
  </div>

  <ProductListEmpty v-else />
</template>

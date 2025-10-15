<script setup>
import {Link} from "@inertiajs/vue3";
import ProductImage from "@/components/modules/products/ProductImage.vue";
import PriceFormatter from "@/components/shared/PriceFormatter.vue";

const props = defineProps({
  product: Object,
})

</script>

<template>
<div class="relative">
  <Link :href="route('catalog.product', product.id)">
    <ProductImage :product="product" />
  </Link>

  <div class="mt-5">
    <div v-if="product.price.has_discount" class="flex items-center">
        <PriceFormatter class="block text-2xl text-destructive font-semibold mr-2" :value="product.price.current"/>
        <PriceFormatter class="block text-sm text-muted-foreground line-through" :value="product.price.base"/>
    </div>
    <PriceFormatter v-else class="text-2xl font-semibold" :value="product.price.current"/>
  </div>

  <div class="mt-3 line-clamp-3 text-sm leading-5" :title="product.name">
    <Link :href="route('catalog.product', product.id)" class="hover:text-black transition-colors">
      {{ product.name }}
    </Link>
  </div>
</div>
</template>
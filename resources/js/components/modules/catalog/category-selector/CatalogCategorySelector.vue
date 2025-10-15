<script setup>
import {cn} from "@/lib/utils";
import CategoriesNavTree from "@/components/modules/catalog/category-selector/CategoriesNavTree.vue";
import {Link} from "@inertiajs/vue3";

const props = defineProps({
  categories: Array
})
</script>

<template>
  <Link
    :href="route('catalog')"
    :class="cn(
        'flex items-center justify-between -mx-[1rem] my-1 whitespace-nowrap rounded-md text-sm font-medium outline-none',
        'transition-colors hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 cursor-pointer',
        route().current('catalog') ? 'bg-accent text-accent-foreground' : ''
      )"
  >
    Все
  </Link>

  <CategoriesNavTree :categories="categories"/>
</template>

<style scoped>

</style>
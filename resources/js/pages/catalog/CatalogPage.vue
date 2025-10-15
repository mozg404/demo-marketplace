<script setup>
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Input from "@/components/ui/input/Input.vue";
import {Label} from "@/components/ui/label/index.js";
import {Switch} from '@/components/ui/switch'
import LaravelPagination from "@/components/shared/LaravelPagination.vue";
import PageLayout from "@/layouts/PageLayout.vue";
import SidebarLayout from "@/components/shared/SidebarLayout.vue";
import CatalogProductList from "@/components/modules/catalog/CatalogProductList.vue";
import CatalogCategorySelector from "@/components/modules/catalog/category-selector/CatalogCategorySelector.vue";
import Box from "@/components/shared/card/Box.vue";
import debounce from "lodash/debounce.js";
import {onUnmounted, watch} from "vue";
import SearchInput from "@/components/shared/form/SearchInput.vue";
import CatalogSortSelect from "@/components/modules/catalog/CatalogSortSelect.vue";
import {Button} from "@/components/ui/button/index.js";
import {X} from 'lucide-vue-next';

const props = defineProps({
  categories: Array,
  products: Object,
  filtersValues: Object,
})

const currentUrl = route(route().current())

const filters = useForm({
  price_min: props.filtersValues?.price_min ?? null,
  price_max: props.filtersValues?.price_max ?? null,
  is_discounted: props.filtersValues?.is_discounted ?? false,
  sort: props.filtersValues?.sort,
  search: props.filtersValues?.search ?? '',
})

const applyFilters = debounce(() => {
  filters.get(currentUrl, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}, 500);

// Следим за ЛЮБЫМ изменением в filters
watch(
  () => filters.data(),
  () => {
    applyFilters()
  },
  {deep: true}
);

// Не забываем почистить таймер
onUnmounted(() => {
  applyFilters.cancel();
});
</script>

<template>
  <PageLayout :with-breadcrumbs="false">
    <template #title>Каталог товаров</template>
    <template #counter>{{ products.total }}</template>

    <Wrapper>
      <SidebarLayout>
        <template #sidebar_left>
          <Box title="Категории">
            <CatalogCategorySelector :categories="categories"/>
          </Box>

          <Box title="Фильтры">
            <div class="space-y-8">
              <div class="flex items-center space-x-2">
                <Switch id="onlyDiscounted" v-model="filters.is_discounted"/>
                <Label for="onlyDiscounted" class="font-normal">Только со скидками</Label>
              </div>

              <div class="flex">
                <div class="grid gap-2 mr-4">
                  <Label for="price_min" class="font-normal">Мин. цена</Label>
                  <Input id="price_min" type="text" autofocus autocomplete="price_min" v-model="filters.price_min"/>
                </div>

                <div class="grid gap-2">
                  <Label for="price_max" class="font-normal">Макс. цена</Label>
                  <Input id="price_max" type="text" autofocus autocomplete="price_max" v-model="filters.price_max"/>
                </div>
              </div>

              <Button variant="secondary" as-child>
                <Link :href="currentUrl">
                  <X/>
                  Сбросить
                </Link>
              </Button>
            </div>
          </Box>
        </template>

        <div class="flex space-x-8 items-center justify-between mb-6">
          <div>
            <SearchInput v-model="filters.search"/>
          </div>

          <div class="text-right flex items-center space-x-5">
            <div class="flex items-center space-x-2">
              <span class="text-sm text-muted-foreground">Сортировка</span>
              <CatalogSortSelect v-model="filters.sort"/>
            </div>
          </div>
        </div>

        <CatalogProductList :products="products.data"/>
        <LaravelPagination class="mt-8" :pagination="products"/>
      </SidebarLayout>
    </Wrapper>
  </PageLayout>
</template>
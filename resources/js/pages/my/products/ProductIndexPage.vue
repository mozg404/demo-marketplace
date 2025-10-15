<script setup>
import {PlusIcon, Ellipsis, Eye, ListPlus, Pencil, ArrowDownNarrowWide, ArrowUpNarrowWide} from "lucide-vue-next";
import {Button} from "@/components/ui/button/index.js";
import { ref, watch } from 'vue';
import {Link, router} from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import {
  Table,
  TableBody,
  TableCell, TableEmpty,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table/index.js'
import Input from "../../../components/ui/input/Input.vue";
import TableBordered from "@/components/shared/table/TableBordered.vue";
import DateTime from "@/components/shared/DateTime.vue";
import PriceFormatter from "@/components/shared/PriceFormatter.vue";
import ProductImage from "@/components/modules/products/ProductImage.vue";
import MainLayout from "@/layouts/MainLayout.vue";
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger
} from "@/components/ui/dropdown-menu/index.js";
import Main from "@/components/shared/layout/Main.vue";
import PageTitle from "@/components/shared/layout/PageTitle.vue";
import ProductStatus from "@/components/modules/products/ProductStatus.vue";
import PageLayout from "@/layouts/PageLayout.vue";
import SearchInput from "@/components/shared/form/SearchInput.vue";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import LaravelPagination from "@/components/shared/LaravelPagination.vue";

const props = defineProps({
  products: Array,
  shops: Array,
  filters: Object,
})
const search = ref(props.filters.search);
const status = ref(props.filters.status);
const sort = ref(props.filters.sort);

// Общая функция для обработки фильтров
const applyFilters = debounce(() => {
  router.get(route('my.products'), {
    search: search.value,
    status: status.value,
    sort: sort.value,
  }, {
    preserveState: true,
    replace: true,
  });
}, 300);

// Наблюдаем за изменениями обоих фильтров
watch([search, status, sort], applyFilters);
</script>

<template>
  <PageLayout :with-breadcrumbs="false">
    <template #title>Мои товары</template>
    <template #counter>{{ products.total }}</template>
    <template #actions>
      <Button as-child>
        <ModalLink :href="route('my.products.create')">
          <PlusIcon class="w-4 h-4"/> Создать товар
        </ModalLink>
      </Button>
    </template>


    <Wrapper>

      <div class="flex space-x-8 items-center justify-between mb-6">
        <div>
          <SearchInput v-model="search"/>
        </div>

        <div class="text-right flex items-center space-x-5">
          <div class="flex items-center space-x-2">
            <span class="text-sm text-muted-foreground">Статус</span>

            <Select v-model="status">
              <SelectTrigger class="w-[150px]">
                <SelectValue placeholder="Выбрать" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem value="all">Все</SelectItem>
                  <SelectItem value="available">Продается</SelectItem>
                  <SelectItem value="sold_out">Закончился</SelectItem>
                  <SelectItem value="draft">Черновик</SelectItem>
                  <SelectItem value="paused">На паузе</SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
          </div>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-muted-foreground">Сортировка</span>

            <Select v-model="sort">
              <SelectTrigger class="w-[200px]">
                <SelectValue placeholder="Выбрать" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem value="latest">Сначала новые</SelectItem>
                  <SelectItem value="oldest">Сначала старые</SelectItem>
                  <SelectItem value="price_desc">Сначала дорогие</SelectItem>
                  <SelectItem value="price_asc">Сначала дешевые</SelectItem>
                  <SelectItem value="id_desc">По ID (вверх)</SelectItem>
                  <SelectItem value="id_asc">По ID (вниз)</SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
          </div>
        </div>
      </div>

      <TableBordered>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Название</TableHead>
              <TableHead>Дата</TableHead>
              <TableHead>Цена</TableHead>
              <TableHead>Статус</TableHead>
              <TableHead>Позиций</TableHead>
              <TableHead></TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableEmpty v-if="products.data?.length === 0" :colspan="6">Товаров не найдено</TableEmpty>
            <TableRow v-for="product in products.data" :key="product.id">
              <TableCell class="whitespace-normal w-1/2">
                <div class="flex items-center">
                  <div class="w-14 shrink-0 mr-4">
                    <Link :href="route('catalog.product', product.id)" class="block">

                      <ProductImage v-if="product?.preview?.small" :product="product" conversion="small"/>
                    </Link>
                  </div>
                  <Link :href="route('my.products.edit', product.id)" class="text-sm">
                    {{product.name}}
                    <div class="text-muted-foreground text-xs mt-2">
                      #{{product.id}}
                    </div>
                  </Link>
                </div>
              </TableCell>
              <TableCell>
                <div class="text-muted-foreground text-xs mt-2">
                  <DateTime :value="product.created_at" format="DD.MM.YYYY HH:mm"/>
                </div>
              </TableCell>

              <TableCell>
                <template v-if="product.price.has_discount">
                  <PriceFormatter :value="product.price.discount" class="font-semibold text-destructive block"/>
                  <PriceFormatter :value="product.price.base" class="text-xs text-muted-foreground line-through block"/>
                </template>
                <PriceFormatter v-else :value="product.price.current" class="font-semibold"/>
              </TableCell>
              <TableCell>
                <ProductStatus :product="product"/>
              </TableCell>

              <TableCell>
                <span class="mr-1">{{ product.stock_items_count }}</span>
                <span title="Доступных позиций">({{ product.available_stock_items_count }})</span>
              </TableCell>

              <TableCell class="text-right">
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon" class="rounded-3xl">
                      <Ellipsis class="w-4 h-4"/>
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent class="w-44">
                    <DropdownMenuItem class="cursor-pointer h-full w-full" :as="Link" :href="route('catalog.product', product.id)">
                      <Eye />Страница товара
                    </DropdownMenuItem>
                    <DropdownMenuSeparator/>
                    <DropdownMenuItem class="cursor-pointer h-full w-full" :as="Link" :href="route('my.products.stock', product.id)">
                      <ListPlus /> Склад
                    </DropdownMenuItem>
                    <DropdownMenuItem class="cursor-pointer h-full w-full" :as="Link" :href="route('my.products.edit', product.id)">
                      <Pencil /> Редактировать
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </TableBordered>

      <LaravelPagination class="mt-6" :pagination="products"/>

    </Wrapper>
  </PageLayout>
</template>
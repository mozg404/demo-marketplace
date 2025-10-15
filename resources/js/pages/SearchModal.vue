<script setup>
import {Modal} from '@inertiaui/modal-vue'
import {ref, watch} from "vue";
import {Search} from 'lucide-vue-next';
import {Input} from "@/components/ui/input/index.js";
import debounce from "lodash/debounce.js";
import axios from 'axios'
import {Link} from "@inertiajs/vue3";
import ProductImage from "@components/modules/products/ProductImage.vue";
import PriceFormatter from "@components/shared/PriceFormatter.vue";

const searchValue = ref('')
const searchResults = ref([])
const isLoading = ref(false)
const modalRef = ref(null)

const applySearch = debounce(async () => {
  if (!searchValue.value.trim()) {
    searchResults.value = []
    return
  }

  isLoading.value = true
  try {
    const response = await axios.post(route('search.store'), {
      search: searchValue.value
    }, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })

    searchResults.value = response.data
  } catch (error) {
    console.error('Search error:', error)
    searchResults.value = []
  } finally {
    isLoading.value = false
  }
}, 500)

const clickSearchLink = () => modalRef.value.close()

watch(searchValue, (newValue) => {
  applySearch()
})
</script>

<template>
  <Modal max-width="xl" ref="modalRef">
    <div class="relative w-full items-center py-4">
      <Input
        id="globalSearch"
        type="text"
        placeholder="Поиск..."
        v-model="searchValue"
        class="pl-10 w-full"
      />
      <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
        <Search class="size-6 text-muted-foreground" />
      </span>
    </div>

    <!-- Индикатор загрузки -->
    <div v-if="isLoading" class="p-6 text-center flex justify-center">
      <div class="animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-primary"></div>
    </div>

    <!-- Результаты поиска -->
    <div v-else-if="searchResults.length > 0" class="mt-4 max-h-96 overflow-y-auto">
      <Link
        v-for="product in searchResults"
        :key="product.id"
        :href="route('catalog.product', product.id)"
        @click="clickSearchLink"
        class="py-3 border-b hover:bg-muted/50 cursor-pointer transition-colors flex"
      >
        <div class="w-8 mr-3">
          <ProductImage :product="product" conversion="thumb"/>
        </div>

        <div class="flex-1">
          {{ product.name }}
          <div class="flex items-center space-x-2" v-if="product.price.has_discount">
            <div class="text-md font-bold">
              <PriceFormatter :value="product.price.discount"/>
            </div>
            <div class="text-muted-foreground line-through">
              <PriceFormatter :value="product.price.base"/>
            </div>
          </div>
          <div v-else class="text-md font-bold">
            <PriceFormatter :value="product.price.current"/>
          </div>
        </div>
      </Link>
    </div>

    <!-- Сообщение, если нет результатов -->
    <div v-else-if="searchValue" class="p-4 text-center text-muted-foreground">
      Товаров не найдено
    </div>

    <!-- Подсказка для начала поиска -->
    <div v-else class="p-4 text-center text-muted-foreground">
      Начните ввод для поиска товаров...
    </div>
  </Modal>
</template>
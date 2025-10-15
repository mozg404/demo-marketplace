<script setup>
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import ProductList from "@/components/modules/products/ProductList.vue";
import PageLayout from "@/layouts/PageLayout.vue";
import PageTitle from "@components/shared/layout/PageTitle.vue";
import {Separator} from "@components/ui/separator";

defineProps({
  discounted: Array,
  games: Array,
  certificates: Array,
  subscriptions: Array,
})

</script>

<template>
  <PageLayout>
    <template #title>Распродажа</template>

    <Wrapper>

      <div class="space-y-12">
        <ProductList :products="discounted" />

        <Separator/>

        <PageTitle>Игры</PageTitle>
        <ProductList :products="games" />

        <Separator/>

        <PageTitle>Подписки</PageTitle>
        <ProductList :products="subscriptions" />

        <Separator/>

        <PageTitle>Сертификаты</PageTitle>
        <ProductList :products="certificates" />
      </div>

    </Wrapper>
  </PageLayout>
</template>
<script setup>
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import {Link} from '@inertiajs/vue3'
import {Button} from '@/components/ui/button/index.js'
import PageTitle from "@/components/shared/layout/PageTitle.vue";
import PageLayout from "@/layouts/PageLayout.vue";
</script>

<template>
  <PageLayout>
    <Wrapper class="text-center">
        <PageTitle class="pb-5">Корзина пустая</PageTitle>
        <Button :as="Link" :href="route('catalog')">К покупкам!</Button>
    </Wrapper>
  </PageLayout>
</template>
<script setup>
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import PageTitle from "@/components/shared/layout/PageTitle.vue";
import sandboxImage from "../../../img/sandbox.jpg";
import {Link, useForm} from "@inertiajs/vue3";
import {Button} from "@/components/ui/button/index.js";
import {
  Card,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card/index.js'
import PriceFormatter from "@/components/shared/PriceFormatter.vue";

const props = defineProps({
  hash: String,
  amount: Number,
})
</script>

<template>
  <Wrapper class="mt-10">

    <div class="flex items-center justify-center h-screen">
      <div class="p-8 text-center">
        <PageTitle class="pb-2">Тут должна быть касса</PageTitle>
        <div class="text-gray-500">Но это же демо проект ;)</div>

        <Card class="max-w-[300px] mx-auto my-10 shadow-none">
          <CardHeader>
            <CardTitle>К оплате: <PriceFormatter :value="amount"/></CardTitle>
          </CardHeader>
          <CardFooter class="flex justify-center">
            <Button :as="Link" :href="route('sandbox.success', hash)" class="mx-2 cursor-pointer">Успех</Button>
            <Button :as="Link" :href="route('sandbox.failed', hash)" variant="destructive" class="mx-2 cursor-pointer">Провал</Button>
            <Button :as="Link" :href="route('sandbox.cancelled', hash)" variant="secondary" class="mx-2 cursor-pointer">Отмена</Button>
          </CardFooter>
        </Card>

        <div>
          <img :src="sandboxImage" alt="Просто изображение"/>
        </div>
      </div>
    </div>
  </Wrapper>
</template>

<script>
import AppLayout from "@/layouts/AppLayout.vue";

export default {
  layout: AppLayout,
}
</script>
<script setup>
import TableBordered from "@/components/shared/table/TableBordered.vue";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table/index.js";
import LaravelPagination from "@/components/shared/LaravelPagination.vue";
import DateTime from "@/components/shared/DateTime.vue";
import PriceFormatter from "@/components/shared/PriceFormatter.vue";
import {Badge} from "@/components/ui/badge/index.js";
import {Button} from "@/components/ui/button/index.js";
import {ChevronRight} from "lucide-vue-next";
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import {Link} from "@inertiajs/vue3";
import PageLayout from "@/layouts/PageLayout.vue";
import OrderStatus from "@/components/modules/orders/OrderStatus.vue";

const props = defineProps({
  orders: Array,
})
</script>

<template>
  <PageLayout :with-breadcrumbs="false">
    <template #title>Заказы</template>
    <template #counter>{{ orders.total }}</template>

    <Wrapper>
      <TableBordered>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Номер</TableHead>
              <TableHead>Дата</TableHead>
              <TableHead>Позиций</TableHead>
              <TableHead>Статус</TableHead>
              <TableHead>Сумма</TableHead>
              <TableHead></TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="order in orders.data" :key="order.id" class="align-middle">
              <TableCell class="font-medium">Заказ #{{ order.id }}</TableCell>
              <TableCell>
                <DateTime :value="order.created_at" format="DD-MM-YYYY в HH:mm"/>
              </TableCell>
              <TableCell>{{ order.items_count }}</TableCell>
              <TableCell>
                <OrderStatus :status="order.status"/>
              </TableCell>
              <TableCell>
                <PriceFormatter class="font-bold" :value="order.amount"/>
              </TableCell>
              <TableCell class="text-right">
                <Button :as="Link" :href="route('my.orders.show', order.id)" variant="secondary" size="icon" class="w-8 h-8">
                  <ChevronRight/>
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </TableBordered>

      <LaravelPagination class="mt-6" :pagination="orders"/>
    </Wrapper>
  </PageLayout>
</template>

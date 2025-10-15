<script setup>
import {PlusIcon} from "lucide-vue-next";
import {Button} from "@/components/ui/button/index.js";
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table/index.js"
import {
  Card,
  CardContent,
} from '@/components/ui/card/index.js'
import DateTime from "@/components/shared/DateTime.vue";
import PriceFormatter from "@/components/shared/PriceFormatter.vue";
import FormNumberInput from "@/components/shared/form/FormNumberInput.vue";
import {Link, useForm} from "@inertiajs/vue3";
import ErrorMessage from "@/components/shared/ErrorMessage.vue";
import { useUser } from '@/composables/useUser.js'
import LaravelPagination from "@/components/shared/LaravelPagination.vue";
import TableBordered from "@/components/shared/table/TableBordered.vue";
import PageLayout from "@/layouts/PageLayout.vue";

const { user } = useUser()
const form = useForm({
  amount: null,
})
const props = defineProps({
  pagination: Object,
})
</script>

<template>
  <PageLayout>
    <template #title>Баланс</template>

    <Wrapper>
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">
        <aside class="xl:col-span-3">
          <Card class="py-4 shadow-none">
            <CardContent class="px-4">
              <div><small>Баланс</small></div>
              <PriceFormatter class="font-bold text-2xl block mt-1" :value="user.balance" />
            </CardContent>
          </Card>

          <Card class="py-4 shadow-none mt-6">
            <CardContent class="px-4">
              <div><small>Пополнить</small></div>

              <form @submit.prevent="form.post(route('my.balance.deposit'))">
                <FormNumberInput placeholder="Сумма" v-model="form.amount" class="mt-2 mb-4"/>
                <ErrorMessage :message="form.errors.amount"/>

                <Button type="submit" class="cursor-pointer">
                  <PlusIcon class="w-4 h-4" />
                  Пополнить
                </Button>
              </form>
            </CardContent>
          </Card>
        </aside>

        <div class="xl:col-span-9 mr-2">

          <TableBordered>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead class="w-[150px]"> Дата</TableHead>
                  <TableHead>Описание</TableHead>
                  <TableHead class="text-right">Сумма</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="transaction in pagination.data" :key="transaction.id">
                  <TableCell class="w-[150px] pr-4">
                    <DateTime :value="transaction.created_at" format="DD-MM-YYYY в HH:mm"/>
                  </TableCell>
                  <TableCell class="font-medium">
                    <div v-if="transaction.type === 'gateway_deposit'">Пополнение баланса</div>
                    <div v-else-if="transaction.type === 'order_payment'">
                      <Link :href="route('my.orders.show', transaction.transactionable_id)">
                        Оплата заказа #{{ transaction.transactionable_id }}
                      </Link>
                    </div>
                    <div v-else-if="transaction.type === 'seller_payout'">Продажа товара</div>
                    <div v-else-if="transaction.type === 'admin_correction'">Корректировка администрацией</div>
                    <div v-else class="text-destructive">Неизвестный тип транзакции</div>
                  </TableCell>
                  <TableCell class="text-right">
                    <PriceFormatter v-if="transaction.amount > 0" class="text-primary" :value="transaction.amount" />
                    <PriceFormatter v-else class="text-destructive" :value="transaction.amount" />
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </TableBordered>

          <LaravelPagination class="mt-6" :pagination="pagination" />
        </div>
      </div>

    </Wrapper>
  </PageLayout>
</template>


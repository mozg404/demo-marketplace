<script setup>
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import {Card, CardContent} from "@/components/ui/card/index.js";
import OrderItemCard from "@components/modules/orders/OrderItemCard.vue";
import PriceFormatter from "@/components/shared/PriceFormatter.vue";
import {
  DescriptionItem,
  DescriptionSeparator,
  DescriptionTitle,
  DescriptionValue
} from "@/components/shared/description/index.js";
import DateTime from "@/components/shared/DateTime.vue";
import PageLayout from "@/layouts/PageLayout.vue";
import SidebarLayout from "@/components/shared/SidebarLayout.vue";
import {Button} from "@/components/ui/button/index.js";
import {Link} from "@inertiajs/vue3";

const props = defineProps({
  order: Object,
  items: Array,
  totalAmount: Object,
  totalCount: Number,
})
</script>

<template>
  <PageLayout>
    <template #title>Заказ #{{ order.id }}</template>

    <Wrapper>
      <SidebarLayout>
        <div class="space-y-6">
          <OrderItemCard v-for="item in items" :key="item.product.id" :item="item" :completed="order.status === 'completed'" />
        </div>

        <template #sidebar_right>
          <Card v-if="order.status === 'completed'" class="border-0 bg-primary text-primary-foreground text-center mb-4">
            <CardContent class="flex justify-center">Заказ оплачен</CardContent>
          </Card>
          <Card v-else-if="order.status === 'cancelled'" class="border-0 bg-secondary text-secondary-foreground text-center mb-4">
            <CardContent class="flex justify-center">Заказ отменен</CardContent>
          </Card>
          <Card v-else class="border-0 bg-destructive text-destructive-foreground text-center mb-4">
            <CardContent class="flex justify-center">Ожидает оплаты</CardContent>
          </Card>

          <Card>
            <CardContent>
              <div class="space-y-2">
                <DescriptionItem>
                  <DescriptionTitle class="font-semibold text-card-foreground">Стоимость</DescriptionTitle>
                  <DescriptionValue class="text-sm font-semibold text-card-foreground"><PriceFormatter :value="totalAmount.current ?? 0 "/></DescriptionValue>
                </DescriptionItem>

                <DescriptionItem>
                  <DescriptionTitle class="text-sm">Товаров</DescriptionTitle>
                  <DescriptionSeparator/>
                  <DescriptionValue class="text-sm">{{ totalCount }}шт</DescriptionValue>
                </DescriptionItem>

                <DescriptionItem>
                  <DescriptionTitle class="text-sm">Дата</DescriptionTitle>
                  <DescriptionSeparator/>
                  <DescriptionValue class="text-sm">
                    <DateTime :value="order.created_at" format="DD.MM.YYYY"/>
                  </DescriptionValue>
                </DescriptionItem>

                <DescriptionItem>
                  <DescriptionTitle class="text-sm">Время</DescriptionTitle>
                  <DescriptionSeparator/>
                  <DescriptionValue class="text-sm">
                    <DateTime :value="order.created_at" format="HH:mm"/>
                  </DescriptionValue>
                </DescriptionItem>

                <DescriptionItem>
                  <DescriptionTitle class="text-sm">Цена</DescriptionTitle>
                  <DescriptionSeparator/>
                  <DescriptionValue class="text-sm"><PriceFormatter :value="totalAmount.base"/></DescriptionValue>
                </DescriptionItem>

                <DescriptionItem>
                  <DescriptionTitle class="text-sm">Скидка</DescriptionTitle>
                  <DescriptionSeparator/>
                  <DescriptionValue class="text-sm"><PriceFormatter :value="totalAmount.discount_amount ?? 0"/></DescriptionValue>
                </DescriptionItem>

                <DescriptionItem>
                  <DescriptionTitle class="text-sm">Выгода</DescriptionTitle>
                  <DescriptionSeparator/>
                  <DescriptionValue class="text-sm"><PriceFormatter :value="totalAmount.discount_percent ?? 0" currency="%"/></DescriptionValue>
                </DescriptionItem>
              </div>

              <div class="mt-8" v-if="order.status === 'pending'">
                <Button :as="Link" :href="route('my.orders.pay', order.id)" class="w-full py-6">Оплатить</Button>
                <Button :as="Link" :href="route('my.orders.cancel', order.id)" class="w-full mt-2" variant="ghost">Отменить заказ</Button>
              </div>
            </CardContent>
          </Card>
        </template>
      </SidebarLayout>
    </Wrapper>
  </PageLayout>
</template>

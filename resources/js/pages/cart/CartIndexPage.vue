<script setup>
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import CartItemCard from "@/components/modules/cart/CartItemCard.vue";
import {Link} from '@inertiajs/vue3'
import {Button} from '@/components/ui/button/index.js'
import {
  Card,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card/index.js'
import PriceFormatter from "@/components/shared/PriceFormatter.vue";
import {Trash,ShoppingBasket} from 'lucide-vue-next'
import {useCart} from "@/composables/useCart.js";
import PageLayout from "@/layouts/PageLayout.vue";
import SidebarLayout from "@/components/shared/SidebarLayout.vue";

const { cart, clearCart, form } = useCart()
</script>

<template>
  <PageLayout>
    <template #title>Корзина</template>
    <template #counter>{{ cart.count }}</template>

    <Wrapper>
      <SidebarLayout>
        <CartItemCard v-for="item in cart.items" :key="item.id" :item="item" />
        <Button
          variant="destructive"
          class="cursor-pointer"
          @click="clearCart()"
          :disabled="form.processing"
          :class="{ 'opacity-50': form.processing }"
        >
          <Trash class="w-4 h-4" />
          Очистить корзину
        </Button>

        <template #sidebar_right>
          <Card>
            <CardHeader>
              <CardTitle class="flex justify-between mb-3">
                <span>К оплате:</span>
                <span><PriceFormatter :value="cart.amount.current"/></span>
              </CardTitle>
              <CardDescription class="flex justify-between">
                <span>Товаров:</span>
                <span>{{cart.quantity}}шт.</span>
              </CardDescription>
              <CardDescription class="flex justify-between">
                <span>Цена:</span>
                <span><PriceFormatter :value="cart.amount.base"/></span>
              </CardDescription>
              <CardDescription class="flex justify-between">
                <span>Скидка:</span>
                <span><PriceFormatter :value="cart.amount.discount_amount"/></span>
              </CardDescription>
              <CardDescription class="flex justify-between">
                <span>Экономия:</span>
                <span><PriceFormatter :value="cart.amount.discount_percent" currency="%"/></span>
              </CardDescription>
            </CardHeader>
            <CardFooter class="border-t-1 pt-5">
              <Button class="w-full" as-child>
                <Link class="cursor-pointer" :href="route('checkout')">
                  <ShoppingBasket class="w-4 h-4" />
                  Оформить заказ
                </Link>
              </Button>
            </CardFooter>
          </Card>
        </template>
      </SidebarLayout>

    </Wrapper>
  </PageLayout>
</template>
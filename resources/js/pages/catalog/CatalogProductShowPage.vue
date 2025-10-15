<script setup>
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import {Link} from "@inertiajs/vue3";
import {Button} from '@/components/ui/button/index.js'
import {Badge} from '@/components/ui/badge/index.js'
import {Minus, Plus, ShoppingCart, Pencil, Eye, Star, ThumbsUp, ThumbsDown} from 'lucide-vue-next';
import ProductImage from "@/components/modules/products/ProductImage.vue";
import PriceFormatter from "@/components/shared/PriceFormatter.vue";
import PageTitle from "@/components/shared/layout/PageTitle.vue";
import {
  DescriptionList,
  DescriptionItem,
  DescriptionTitle,
  DescriptionValue,
  DescriptionSeparator
} from '@/components/shared/description/index.js'
import Section from "@/components/shared/layout/Section.vue";
import SectionTitle from "@/components/shared/layout/SectionTitle.vue";
import { useCart } from '@/composables/useCart.js'
import {Card, CardContent} from "@/components/ui/card/index.js";
import UserAvatarIcon from "@components/modules/users/UserAvatarIcon.vue";
import PageLayout from "@/layouts/PageLayout.vue";
import SidebarLayout from "@/components/shared/SidebarLayout.vue";
import FeedbackCard from "@/components/modules/feedback/FeedbackCard.vue";
import RatingColor from "@/components/shared/RatingColor.vue";

const props = defineProps({
  product: Object,
  feedbacks: Array,
  isOwner: {
    type: Boolean,
    default: false,
  },
})
const { inCart, addToCart, decreaseQuantity, getCartItemQuantity, form } = useCart()
</script>

<template>
  <PageLayout>
    <template v-if="isOwner" #after-header>
      <div class="py-3 bg-gray-50 border-b-1">
        <Wrapper>
          <div class="flex items-center justify-between">
            <div class="flex text-sm font-semibold items-center">
              <Eye class="mr-2 text-primary" />
              Ваш товар
            </div>

            <div class="space-x-3">
              <Button variant="outline" as-child>
                <Link :href="route('my.products.edit', product.id)" >
                  <Pencil />
                  Редактировать
                </Link>
              </Button>
            </div>

          </div>
        </Wrapper>
      </div>
    </template>

    <Wrapper>
      <SidebarLayout class="lg:sticky lg:top-6 lg:self-start">


        <div class="mb-12">
          <Badge variant="secondary">{{ product.category.name }}</Badge>
          <PageTitle class="mt-4">{{ product.name }}</PageTitle>

          <div class="flex items-center space-x-6 mt-4">
            <div class="flex items-center ">
              <Star class="h-5 w-5 mr-2" />
              <span class="text-sm">{{ product.rating }}%</span>
            </div>

            <div class="flex items-center">
              <ThumbsUp class="h-5 w-5 mr-2 text-green-800"/>
              <span class="text-sm">{{ product.positive_feedbacks_count }}</span>
            </div>

            <div class="flex items-center">
              <ThumbsDown class="h-5 w-5 mr-2 text-destructive" />
              <span class="text-sm">{{ product.negative_feedbacks_count }}</span>
            </div>
          </div>
        </div>

        <Section v-if="product.features" class="mt-6">
          <DescriptionList>
            <DescriptionItem v-for="feature in product.features" :key="feature.id">
              <DescriptionTitle>{{ feature.name }}</DescriptionTitle>
              <DescriptionSeparator />
              <DescriptionValue>{{ feature.value }}</DescriptionValue>
            </DescriptionItem>
          </DescriptionList>
        </Section>

        <Section v-if="product.description" class="mt-12">
          <SectionTitle>Описание</SectionTitle>

          <article class="mt-2 prose prose-md max-w-full">
            <div v-html="product.description"></div>
          </article>
        </Section>

        <Section v-if="product.instruction">
          <SectionTitle>Инструкция по активации</SectionTitle>

          <article class="mt-2 prose prose-md">
            <div v-html="product.instruction"></div>
          </article>
        </Section>

        <Section v-if="feedbacks?.length > 0">
          <SectionTitle class="mb-7">Отзывы</SectionTitle>

          <div class="space-y-7">
            <FeedbackCard v-for="feedback in feedbacks" :key="feedback.id" :feedback="feedback"/>
          </div>
        </Section>

        <template #sidebar_right>
          <ProductImage :product="product" conversion="large" class="mb-4"/>

          <Card class="py-4 mb-4">
            <CardContent class="px-4">

              <div class="text-muted-foreground text-sm pb-2">В наличие: {{product.available_stock_items_count}}</div>
              <div class="mb-4">
                <div class="flex items-center space-x-2" v-if="product.price.has_discount">
                  <div class="text-3xl font-bold">
                    <PriceFormatter :value="product.price.discount"/>
                  </div>
                  <div class="text-muted-foreground line-through">
                    <PriceFormatter :value="product.price.base"/>
                  </div>
                </div>
                <div v-else class="text-3xl font-bold">
                  <PriceFormatter :value="product.price.current"/>
                </div>
              </div>

              <template v-if="product.status === 'active'">
                <div v-if="inCart(product.id)">
                  <div class="flex space-x-4 items-center">
                    <div class="bg-gray-100 rounded-3xl flex items-center gap-3">
                      <Button
                        variant="outline"
                        class="rounded-3xl cursor-pointer hover:bg-destructive hover:text-destructive-foreground hover:border-destructive"
                        size="icon"
                        :disabled="form.processing"
                        @click="decreaseQuantity(product.id)"
                      >
                        <Minus class="w-4 h-4"/>
                      </Button>

                      <div>{{getCartItemQuantity(product.id)}}</div>

                      <Button
                        variant="outline"
                        class="rounded-3xl cursor-pointer hover:bg-primary hover:text-primary-foreground hover:border-primary"
                        size="icon"
                        @click="addToCart(product.id)"
                        :disabled="form.processing"
                      >
                        <Plus class="w-4 h-4"/>
                      </Button>
                    </div>
                    В корзине
                    <Button :as="Link" :href="route('cart.index')" class="rounded-3xl cursor-pointer hidden">
                      В корзине
                    </Button>
                  </div>
                </div>

                <div v-else class="flex">
                  <Button :as="Link" :href="route('checkout.express', product.id)" size="lg" class="cursor-pointer w-full flex-1 mr-3">
                    Купить
                  </Button>

                  <Button
                    size="icon"
                    variant="secondary"
                    class="cursor-pointer p-5"
                    @click="addToCart(product.id)"
                    :disabled="form.processing"
                  >
                    <ShoppingCart />
                  </Button>
                </div>

              </template>

              <template v-else>
                <Button class="w-full cursor-pointer py-6" disabled>
                  Недоступно для покупки
                </Button>
              </template>
            </CardContent>
          </Card>

          <Card class="py-4 mb-4">
            <CardContent class="px-4 flex items-center">

              <UserAvatarIcon class="mr-3" :user="product.user"/>
              <div class="flex justify-between flex-1">
                <div>
                  <div class="text-muted-foreground text-xs mb-[-4px]">Продавец</div>
                  <Link :href="route('users.show', product.user.id)" class="font-semibold text-sm hover:opacity-50 transition-opacity">{{product.user.name}}</Link>
                </div>
                <div class="text-right">
                  <div class="text-muted-foreground text-xs">Рейтинг</div>
                  <div class="font-semibold text-sm">
                    <RatingColor :rating="product.user.seller_rating"/>%
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </template>

      </SidebarLayout>

    </Wrapper>
  </PageLayout>
</template>

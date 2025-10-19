<script setup>
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import {Link} from "@inertiajs/vue3";
import {Button} from '@/components/ui/button/index.js'
import {Badge} from '@/components/ui/badge/index.js'
import {ShoppingCart, Pencil, TriangleAlert, Ellipsis, Eye, ListPlus} from 'lucide-vue-next';
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
import {Card, CardContent} from "@/components/ui/card/index.js";
import UserAvatarIcon from "@components/modules/users/UserAvatarIcon.vue";
import PageLayout from "@/layouts/PageLayout.vue";
import SidebarLayout from "@/components/shared/SidebarLayout.vue";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import DropdownModalLink from "@/components/shared/dropdown/DropdownModalLink.vue";

const props = defineProps({
  product: Object,
})
</script>

<template>
  <PageLayout>
    <template #after-header>
      <div class="py-3 bg-gray-50 border-b-1">
        <Wrapper>
          <div class="flex items-center justify-between">
            <div class="flex text-sm font-semibold items-center">
              <TriangleAlert class="mr-2 text-destructive" />
              Режим редактирования
            </div>

            <div class="space-x-3">
              <Button variant="outline" as-child>
                <Link :href="route('catalog.product', product.id)" >
                  <Eye />
                  Страница товара
                </Link>
              </Button>

              <Button variant="outline" as-child>
                <Link :href="route('my.products.stock', product.id)" >
                  <ListPlus /> Склад
                </Link>
              </Button>

              <DropdownMenu>
                <DropdownMenuTrigger as-child>
                  <Button variant="outline" size="icon">
                    <Ellipsis />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                  <DropdownModalLink :href="route('my.products.change.base', product.id)">Изменить основу</DropdownModalLink>
                  <DropdownModalLink :href="route('my.products.change.features', product.id)">Изменить характеристики</DropdownModalLink>
                  <DropdownModalLink :href="route('my.products.change.image', product.id)">Изменить изображение</DropdownModalLink>
                  <DropdownModalLink :href="route('my.products.change.description', product.id)">Изменить описание</DropdownModalLink>
                  <DropdownModalLink :href="route('my.products.change.instruction', product.id)">Изменить инструкцию</DropdownModalLink>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>

          </div>
        </Wrapper>
      </div>
    </template>


    <Wrapper>
      <SidebarLayout class="lg:sticky lg:top-6 lg:self-start">

        <div class="mb-12">
          <div class="flex items-center">
            <Badge variant="secondary">{{ product.category.name }}</Badge>

            <Button size="icon" variant="ghost" class="ml-3 p-1 w-6 h-6" as-child>
              <ModalLink :href="route('my.products.change.base', product.id)">
                <Pencil />
              </ModalLink>
            </Button>
          </div>

          <PageTitle class="mt-3">
            {{ product.name }}

            <Button size="icon" variant="ghost" class="ml-2 p-1 w-8 h-8" as-child>
              <ModalLink :href="route('my.products.change.base', product.id)">
                <Pencil />
              </ModalLink>
            </Button>
          </PageTitle>
        </div>

        <Section  class="mt-6">
          <DescriptionList v-if="product.features" class="mb-5">
            <DescriptionItem v-for="feature in product.features" :key="feature.id">
              <DescriptionTitle>{{ feature.name }}</DescriptionTitle>
              <DescriptionSeparator />
              <DescriptionValue>{{ feature.value }}</DescriptionValue>
            </DescriptionItem>
          </DescriptionList>

          <Button variant="secondary" as-child>
            <ModalLink :href="route('my.products.change.features', product.id)">
              <Pencil />
              Изменить характеристики
            </ModalLink>
          </Button>
        </Section>

        <Section class="mt-12">
          <SectionTitle>
            Описание
            <Button size="icon" variant="ghost" as-child>
              <ModalLink :href="route('my.products.change.description', product.id)">
                <Pencil />
              </ModalLink>
            </Button>
          </SectionTitle>

          <article v-if="product.description" class="mt-2 prose prose-md max-w-full">
            <div v-html="product.description"></div>
          </article>
        </Section>

        <Section >
          <SectionTitle>
            Инструкция по активации
            <Button size="icon" variant="ghost" as-child>
              <ModalLink :href="route('my.products.change.instruction', product.id)">
                <Pencil />
              </ModalLink>
            </Button>
          </SectionTitle>

          <article v-if="product.instruction" class="mt-2 prose prose-md">
            <div v-html="product.instruction"></div>
          </article>
        </Section>

        <template #sidebar_right>
          <ModalLink :href="route('my.products.change.image', product.id)">
            <ProductImage :product="product" class="mb-4"/>
          </ModalLink>

          <Card class="py-4 mb-4">
            <CardContent class="px-4">
              <div class="text-muted-foreground text-sm pb-2">В наличие: {{product.available_stock_items_count}}</div>
              <div class="mb-4 flex justify-between">
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

                <Button size="icon" variant="ghost" class="ml-3" as-child>
                  <ModalLink :href="route('my.products.change.base', product.id)">
                    <Pencil />
                  </ModalLink>
                </Button>
              </div>

              <template v-if="product.status === 'active'">
                <div class="flex">
                  <Button size="lg" class="cursor-pointer w-full flex-1 mr-3">Купить</Button>
                  <Button size="icon" variant="secondary" class="cursor-pointer p-5">
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
              <div>
                <div class="text-muted-foreground text-xs mb-[-4px]">Продавец</div>
                <Link :href="route('users.show', product.user.id)" class="font-semibold text-sm hover:text-primary">{{product.user.name}}</Link>
              </div>
            </CardContent>
          </Card>
        </template>
      </SidebarLayout>

    </Wrapper>
  </PageLayout>
</template>

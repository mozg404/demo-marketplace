<script setup>
import {Link} from "@inertiajs/vue3";
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import {Button} from "@/components/ui/button/index.js";
import logoUrl from "@img/logo.svg";
import {ShoppingCart, LogIn, Search, Wallet, Menu} from 'lucide-vue-next'
import {useCart} from '@/composables/useCart.js'
import {useUser} from '@/composables/useUser.js'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu/index.js'
import UserAvatarIcon from "@components/modules/users/UserAvatarIcon.vue";
import PriceFormatter from "@components/shared/PriceFormatter.vue";

const {cart} = useCart()
const {user, isAuth} = useUser()
</script>

<template>
  <header>
    <Wrapper>
      <div class="flex items-center justify-between py-5" aria-label="Global">

        <div class="flex items-center gap-x-6">
          <Link :href="route('home')" class="-m-1.5 p-1.5 hover:opacity-50 transition-opacity">
            <img class="h-10 lg:h-12 w-auto" :src="logoUrl" alt="Free маркетплейс">
          </Link>

          <div class="md:hidden">
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="secondary" class="rounded-3xl -ml-2">
                  <Menu class="w-8 h-8" /> Меню
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent class="w-44">
                <DropdownMenuItem>
                  <Link :href="route('catalog')">Каталог</Link>
                </DropdownMenuItem>
                <DropdownMenuItem>
                  <Link :href="route('users')">Продавцы</Link>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>

          <div class="hidden md:flex md:gap-x-2">
            <Button variant="ghost" class="rounded-3xl" as-child>
              <Link :href="route('catalog')">Каталог</Link>
            </Button>
          </div>

          <div class="hidden md:flex md:gap-x-2">
            <Button variant="ghost" class="rounded-3xl" as-child>
              <Link :href="route('users')">Продавцы</Link>
            </Button>
          </div>
        </div>

        <div class="flex flex-1 justify-end">
          <div class="flex gap-2">
            <Button variant="outline" size="icon" class="rounded-3xl" as-child>
              <ModalLink :href="route('search')">
                <Search class="w-4 h-4"/>
              </ModalLink>
            </Button>

            <Button variant="outline" class="rounded-3xl hidden md:flex" v-if="isAuth" as-child>
              <Link :href="route('my.balance')">
                <Wallet class="w-4 h-4"/>
                <PriceFormatter :value="user.balance"/>
              </Link>
            </Button>

            <Button variant="outline" class="rounded-3xl" as-child>
              <Link :href="route('cart.index')">
                <ShoppingCart class="w-4 h-4"/>
                {{ cart.quantity }}
              </Link>
            </Button>

            <template v-if="isAuth" class="hidden">
              <DropdownMenu>
                <DropdownMenuTrigger as-child>
                  <UserAvatarIcon :user="user" class="hover:opacity-50 transition-opacity cursor-pointer"/>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-44">
                  <DropdownMenuLabel>
                    {{ user.name }}
                    <div class="font-normal text-muted-foreground">{{ user.email }}</div>
                  </DropdownMenuLabel>
                  <DropdownMenuSeparator/>
                  <DropdownMenuItem>
                    <Link :href="route('my.balance')" class="block w-full h-full">Баланс</Link>
                  </DropdownMenuItem>
                  <DropdownMenuItem>
                    <Link :href="route('my.orders')" class="block w-full h-full">Заказы</Link>
                  </DropdownMenuItem>
                  <DropdownMenuItem>
                    <Link :href="route('my.purchases')" class="block w-full h-full">Покупки</Link>
                  </DropdownMenuItem>
                  <DropdownMenuItem>
                    <Link :href="route('my.products')" class="block w-full h-full">Товары</Link>
                  </DropdownMenuItem>
                  <DropdownMenuItem>
                    <Link :href="route('my.sales')" class="block w-full h-full">Продажи</Link>
                  </DropdownMenuItem>
                  <DropdownMenuSeparator/>
                  <DropdownMenuItem>
                    <Link :href="route('users.show', user.id)" class="block w-full h-full">Профиль</Link>
                  </DropdownMenuItem>
                  <DropdownMenuItem>
                    <Link :href="route('my.settings')" class="block w-full h-full">Настройки</Link>
                  </DropdownMenuItem>
                  <DropdownMenuSeparator/>
                  <DropdownMenuItem>
                    <Link :href="route('logout')" class="block w-full h-full">Выйти</Link>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </template>
            <Button variant="outline" class="rounded-3xl" v-else as-child>
              <Link :href="route('login')">
                <LogIn class="w-4 h-4"/>
                Вход
              </Link>
            </Button>

          </div>
        </div>
      </div>

    </Wrapper>
  </header>
</template>

<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import AppHeader from "../components/modules/app/AppHeader.vue";
import AppBreadcrumbs from "@/components/modules/app/AppBreadcrumbs.vue";
import PageTitle from "@components/shared/layout/PageTitle.vue";
import {useUser} from '@/composables/useUser.js'
import Wrapper from "@components/shared/layout/Wrapper.vue";
import SidebarLink from "@components/shared/sidebar/SidebarLink.vue";
import {Link} from "@inertiajs/vue3";
import UserAvatar from "@components/modules/users/UserAvatar.vue";
import AppFooter from "@components/modules/app/AppFooter.vue";

const {user} = useUser()
const authUser = user

const props = defineProps({
  breadcrumbs: {
    type: Boolean,
    default: true,
  },
})
</script>

<template>
  <AppLayout>
    <div class="h-screen flex flex-col justify-between">
      <div>
        <AppHeader/>
        <AppBreadcrumbs v-if="breadcrumbs" />

        <div class="bg-accent">
          <Wrapper>
            <div class="flex items-center py-10">
              <div class="w-30 shrink-0 mr-4">
                <ModalLink :href="route('my.settings.change.avatar')">
                  <UserAvatar :user="authUser" conversion="large"/>
                </ModalLink>
              </div>
              <div>
                <PageTitle>{{ authUser.name }}</PageTitle>
                <div class="mt-1">{{authUser.email}}</div>
              </div>
            </div>
          </Wrapper>
        </div>

        <Wrapper>
          <div class="flex w-full pt-10">
            <aside class="w-64 flex-none min-w-[20rem] ">
              <div class="max-w-[16rem] space-y-2">
                <SidebarLink :active="route().current('my.settings')" as-child>
                  <Link :href="route('my.settings')">Редактировать профиль</Link>
                </SidebarLink>
                <SidebarLink :active="route().current('my.settings.change.password')" as-child>
                  <Link :href="route('my.settings.change.password')">Изменить пароль</Link>
                </SidebarLink>
                <SidebarLink as-child>
                  <ModalLink :href="route('my.settings.change.avatar')">Изменить аватарку</ModalLink>
                </SidebarLink>
              </div>
            </aside>

            <main class="flex-1">
              <slot/>
            </main>
          </div>
        </Wrapper>

      </div>
      <AppFooter/>
    </div>
  </AppLayout>
</template>
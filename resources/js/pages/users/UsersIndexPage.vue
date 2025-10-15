<script setup>
import PageLayout from "@/layouts/PageLayout.vue";
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table/index.js";
import {Link} from "@inertiajs/vue3";
import TableBordered from "@/components/shared/table/TableBordered.vue";
import LaravelPagination from "@/components/shared/LaravelPagination.vue";
import AccountAge from "@/components/shared/AccountAge.vue";
import UserAvatar from "@components/modules/users/UserAvatar.vue";

const props = defineProps({
  users: Array,
})
</script>

<template>
  <PageLayout :with-breadcrumbs="false">
    <template #title>Продавцы</template>
    <template #counter>{{ users.total }}</template>

    <wrapper>
      <TableBordered>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Продавец</TableHead>
              <TableHead>На сайте</TableHead>
              <TableHead>Товаров</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="user in users.data" :key="user.id">
              <TableCell class="whitespace-normal w-1/2">
                <div class="flex items-center">
                  <div class="w-16 shrink-0 mr-4">
                    <UserAvatar :user="user"/>
                  </div>
                  <div>
                    <Link :href="route('users.show', user.id)" class="font-semibold hover:text-primary transition-colors">
                      {{user.name}}
                    </Link>
                  </div>
                </div>
              </TableCell>
              <TableCell>
                <AccountAge :registration-date="user.created_at"/>
              </TableCell>
              <TableCell>{{ user.available_products_count }}</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </TableBordered>

      <LaravelPagination class="mt-8 justify-center" :pagination="users"/>

    </wrapper>
  </PageLayout>
</template>
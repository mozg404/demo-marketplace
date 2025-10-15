<script setup>
import UserAvatarIcon from "@components/modules/users/UserAvatarIcon.vue";
import {Link} from "@inertiajs/vue3";
import AccountAge from "@/components/shared/AccountAge.vue";

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
})
</script>

<template>
  <div class="flex items-center">
    <UserAvatarIcon class="mr-3" :user="user"/>
    <div>
      <Link :href="route('users.show', user.id)" class="font-semibold text-sm hover:text-primary mb-[-4px]">{{ user.name }}</Link>
      <div class="text-muted-foreground text-xs ">
        На сайте <AccountAge :registration-date="user.created_at"/>
      </div>
    </div>
  </div>
</template>
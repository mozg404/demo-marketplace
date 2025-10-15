<script setup>
import UserAvatarIcon from "@components/modules/users/UserAvatarIcon.vue";
import DateTime from "@/components/shared/DateTime.vue";
import {ThumbsDown, ThumbsUp} from "lucide-vue-next";
import {Link} from "@inertiajs/vue3";

const props = defineProps({
  feedback: {
    type: Object,
    required: true,
  }
})
</script>

<template>
  <div>
    <div class="flex space-x-3 items-center">
      <div class="flex items-center">
        <UserAvatarIcon class="mr-2" :user="feedback.user"/>
        <div class="text-accent-foreground text-sm font-medium">
          <Link class="hover:opacity-50 transition-opacity" :href="route('users.show', feedback.user.id)">
            {{ feedback.user.name }}
          </Link>
        </div>
      </div>

      <ThumbsUp v-if="feedback.is_positive" class="h-4 w-4 text-green-800" />
      <ThumbsDown v-else class="h-4 w-4 text-destructive" />

      <DateTime class="text-sm text-muted-foreground" :value="feedback.created_at" format="D MMMM YYYY в HH:MM"/>
    </div>
    <div class="mt-2">{{ feedback.comment }}</div>
  </div>
</template>
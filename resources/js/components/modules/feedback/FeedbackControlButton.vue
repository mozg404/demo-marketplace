<script setup>
import {Star, ThumbsDown, ThumbsUp} from "lucide-vue-next";
import {Button} from "@/components/ui/button";

const props = defineProps({
  orderItemId: Number,
  feedback: Object,
  variant: {
    type: String,
    default: 'outline',
  }
})
</script>

<template>
  <template v-if="feedback">
    <Button v-if="feedback.is_positive" :variant="variant" class="text-green-800 hover:text-green-800" as-child>
      <ModalLink :href="route('my.orders.item.feedback.edit', [orderItemId, feedback.id])">
        <ThumbsUp/>
        {{ feedback.comment ? 'Позитивный отзыв' : 'Позитивная оценка' }}
      </ModalLink>
    </Button>

    <Button v-else :variant="variant" class="text-destructive hover:text-destructive" as-child>
      <ModalLink :href="route('my.orders.item.feedback.edit', [orderItemId, feedback.id])">
        <ThumbsDown/>
        {{ feedback.comment ? 'Негативный отзыв' : 'Негативная оценка' }}
      </ModalLink>
    </Button>
  </template>

  <Button v-else :variant="variant" as-child>
    <ModalLink :href="route('my.orders.item.feedback.create', orderItemId)">
      <Star />
      Оценить
    </ModalLink>
  </Button>
</template>
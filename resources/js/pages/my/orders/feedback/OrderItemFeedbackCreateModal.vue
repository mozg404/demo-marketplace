<script setup>
import {Modal} from '@inertiaui/modal-vue'
import {ref} from "vue";
import {Label} from "@/components/ui/label/index.js";
import {LoaderCircle} from 'lucide-vue-next';
import {Button} from '@/components/ui/button/index.js'
import {useForm} from "@inertiajs/vue3";
import ErrorMessage from "@/components/shared/ErrorMessage.vue";
import {Textarea} from "@/components/ui/textarea/index.js";
import {ThumbsDown, ThumbsUp} from 'lucide-vue-next';
import {showToastsFromInertiaModal} from "@/composables/useToasts.js";

const props = defineProps({
  orderItem: Object,
  product: Object,
})
const form = useForm({
  is_positive: true,
  comment: '',
})

const modalRef = ref(null)
const submit = () => form.post(route('my.orders.item.feedback.store', props.orderItem.id), {
  onSuccess: (data) => {
    showToastsFromInertiaModal(modalRef, data)
    modalRef.value.close()
  }
})
</script>

<template>
  <Modal max-width="lg" ref="modalRef">
    <div class="text-2xl font-semibold mb-2">Отзыв на покупку</div>
    <div class="text-muted-foreground text-sm mb-8">{{ product.name }}</div>

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid gap-6">

        <div class="grid gap-3">
          <Label as="div">Оценка</Label>
          <div class="flex space-x-3">
            <Button
              type="button"
              variant="outline"
              @click="form.is_positive = true"
              :class="form.is_positive ? 'text-green-800 hover:text-green-800' : ''"
            >
              <ThumbsUp/>
              Мне нравится
            </Button>
            <Button
              type="button"
              variant="outline"
              @click="form.is_positive = false"
              :class="!form.is_positive ? 'text-destructive hover:text-destructive' : ''"
            >
              <ThumbsDown/>
              Мне не нравится
            </Button>
          </div>
        </div>

        <div class="grid gap-3">
          <Label for="comment">Комментарий</Label>
          <Textarea id="comment" v-model="form.comment"/>
          <ErrorMessage :message="form.errors.comment"/>
        </div>

        <div>
          <Button type="submit" tabindex="6" :disabled="form.processing" class="cursor-pointer">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin"/>
            Оценить
          </Button>
        </div>
      </div>
    </form>

  </Modal>
</template>
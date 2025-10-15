<script setup>
import {Modal} from '@inertiaui/modal-vue'
import {ref} from "vue";
import {Label} from "@/components/ui/label/index.js";
import {Button} from '@/components/ui/button/index.js'
import {useForm} from "@inertiajs/vue3";
import ErrorMessage from "@/components/shared/ErrorMessage.vue";
import {Textarea} from "@/components/ui/textarea/index.js";
import {ThumbsDown, ThumbsUp} from 'lucide-vue-next';
import {showToastsFromInertiaModal} from "@/composables/useToasts.js";
import LoaderButton from "@/components/shared/button/ProcessingButton.vue";

const props = defineProps({
  orderItem: Object,
  product: Object,
  feedback: Object,
})
const form = useForm({
  is_positive: props.feedback.is_positive,
  comment: props.feedback.comment,
})
const destroyForm = useForm()
const modalRef = ref(null)
const save = () => form.put(route('my.orders.item.feedback.update', [props.orderItem.id, props.feedback.id]), {
  onSuccess: (data) => {
    showToastsFromInertiaModal(modalRef, data)
    modalRef.value.close()
  }
})

const destroy = () => destroyForm.delete(route('my.orders.item.feedback.destroy', [props.orderItem.id, props.feedback.id]), {
  onSuccess: (data) => {
    showToastsFromInertiaModal(modalRef, data)
    modalRef.value.close()
  }
})
</script>

<template>
  <Modal max-width="lg" ref="modalRef">
    <div class="text-2xl font-semibold mb-2">Изменить отзыв на покупку</div>
    <div class="text-muted-foreground text-sm mb-8">{{ product.name }}</div>

    <form @submit.prevent="save" class="flex flex-col gap-6">
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

        <div class="flex items-center justify-between">
          <LoaderButton :isLoading="destroyForm.processing" @click="destroy" type="button" variant="destructive">
            Удалить
          </LoaderButton>
          <LoaderButton :isLoading="form.processing">
            Сохранить
          </LoaderButton>
        </div>
      </div>
    </form>

  </Modal>
</template>
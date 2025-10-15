<script setup>
import {Modal} from '@inertiaui/modal-vue'
import {ref} from "vue";
import {Label} from "@/components/ui/label/index.js";
import {LoaderCircle} from 'lucide-vue-next';
import {Button} from '@/components/ui/button/index.js'
import {Textarea} from '@/components/ui/textarea/index.js'
import {useForm} from "@inertiajs/vue3";
import ErrorMessage from "@/components/shared/ErrorMessage.vue";

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  stockItem: {
    type: Object,
    required: true,
  },
})
const form = useForm({
  content: props.stockItem.content,
  _method: 'PUT',
})
const modalRef = ref(null)
const submit = () => form.post(route('my.products.stock.update', [props.product.id, props.stockItem.id]), {
  onSuccess: () => modalRef.value.close(),
})

</script>

<template>
  <Modal max-width="md" ref="modalRef">
    <div class="text-2xl font-semibold mb-8">Редактировать ключ</div>

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid gap-6">

        <div class="grid gap-2">
          <Label for="content">Содержимое</Label>
          <Textarea id="content" v-model="form.content" />
          <ErrorMessage :message="form.errors.content"/>
        </div>

        <div>
          <Button type="submit" tabindex="6" :disabled="form.processing" class="cursor-pointer">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin"/>
            Сохранить
          </Button>
        </div>
      </div>
    </form>

  </Modal>
</template>
<script setup>
import {Modal} from '@inertiaui/modal-vue'
import {ref} from "vue";
import {Label} from "@/components/ui/label/index.js";
import {LoaderCircle} from 'lucide-vue-next';
import {Button} from '@/components/ui/button/index.js'
import {useForm} from "@inertiajs/vue3";
import ErrorMessage from "@/components/shared/ErrorMessage.vue";
import CategorySelect from "@/components/modules/products/CategorySelect.vue";
import {showToastsFromFormData} from "@/composables/useToasts.js";

const props = defineProps({
  product: Object,
  categoriesTree: Array,
})
const form = useForm({
  category_id: props.product.category_id,
  _method: 'PATCH',
})

const modalRef = ref(null)
const submit = () => form.post(route('my.products.change.category.update', props.product.id), {
  onSuccess: (data) => {
    showToastsFromFormData(data)
    modalRef.value.close()
  }
})
</script>

<template>
  <Modal max-width="2xl" ref="modalRef">
    <div class="text-2xl font-semibold mb-8">Редактирование категории</div>

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid gap-6">

        <div class="grid gap-3">
          <Label for="category_id">Категория</Label>
          <CategorySelect v-model="form.category_id" :categories="categoriesTree"/>
          <ErrorMessage :message="form.errors.category_id"/>
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
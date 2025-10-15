<script setup>
import {Modal} from '@inertiaui/modal-vue'
import {ref} from "vue";
import {Label} from "@/components/ui/label/index.js";
import {LoaderCircle} from 'lucide-vue-next';
import {Button} from '@/components/ui/button/index.js'
import {useForm} from "@inertiajs/vue3";
import ErrorMessage from "@/components/shared/ErrorMessage.vue";
import {Input} from "@/components/ui/input/index.js";
import CategorySelect from "@/components/modules/products/CategorySelect.vue";
import {
  NumberField,
  NumberFieldContent,
  NumberFieldInput,
} from "@/components/ui/number-field"
import {showToastsFromFormData} from "@/composables/useToasts.js";

const props = defineProps({
  categoriesTree: Object,
})
const form = useForm({
  name: '',
  category_id: '',
  price_base: '',
  price_discount: '',
})

const modalRef = ref(null)
const submit = () => form.post(route('my.products.create.store'), {
  onSuccess: (data) => {
    showToastsFromFormData(data)
    modalRef.value.close()
  }
})
</script>

<template>
  <Modal max-width="2xl" ref="modalRef">
    <div class="text-2xl font-semibold mb-8">Новый товар</div>

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid gap-6">

        <div class="grid gap-3">
          <Label for="category_id">Категория</Label>
          <CategorySelect v-model="form.category_id" :categories="categoriesTree"/>
          <ErrorMessage :message="form.errors.category_id"/>
        </div>

        <div class="grid gap-3">
          <Label for="name">Название</Label>
          <Input id="name" v-model="form.name" />
          <ErrorMessage :message="form.errors.name"/>
        </div>

        <div class="grid grid-cols-2 gap-6 items-start">
          <NumberField id="price_base" v-model="form.price_base" class="gap-3">
            <Label for="price_base">Цена</Label>
            <NumberFieldContent>
              <NumberFieldInput class="text-left px-3 py-1" />
            </NumberFieldContent>
            <ErrorMessage :message="form.errors.price_base"/>
          </NumberField>

          <NumberField id="price_discount" v-model="form.price_discount" class="gap-3" >
            <Label for="price_discount">Цена по скидке</Label>
            <NumberFieldContent>
              <NumberFieldInput class="text-left px-3 py-1" />
            </NumberFieldContent>
            <ErrorMessage :message="form.errors.price_discount"/>
          </NumberField>
        </div>

        <div>
          <Button type="submit" tabindex="6" :disabled="form.processing" class="cursor-pointer">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin"/>
            Создать
          </Button>
        </div>
      </div>
    </form>

  </Modal>
</template>
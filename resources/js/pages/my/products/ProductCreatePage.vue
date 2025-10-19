<script setup>

import ErrorMessage from "@components/shared/ErrorMessage.vue";
import {Label} from "@components/ui/label/index.js";
import {Input} from "@components/ui/input/index.js";
import PageLayout from "@/layouts/PageLayout.vue";
import Wrapper from "@components/shared/layout/Wrapper.vue";
import {useForm} from "@inertiajs/vue3";
import {LoaderCircle} from "lucide-vue-next";
import CategorySelect from "@components/modules/products/CategorySelect.vue";
import {NumberField, NumberFieldContent, NumberFieldInput} from "@components/ui/number-field/index.js";
import {Button} from "@components/ui/button/index.js";
import QuillEditor from "@components/shared/form/QuillEditor.vue";
import InputError from "@components/ui/input/InputError.vue";
import ImageUploader from "@components/shared/form/ImageUploader.vue";
import {showToastsFromFormData} from "@/composables/useToasts.js";

const props = defineProps({
  categoriesTree: Object,
})
const form = useForm({
  name: '',
  category_id: '',
  price_base: '',
  price_discount: '',
  description: '',
  instruction: '',
  preview: null,
})

const submitHandler = (e) => form.post(route('my.products.create.store'), {
  onSuccess: (data) => {

  },
})

</script>

<template>
  <PageLayout>
    <Wrapper>

      <div class="max-w-2xl mx-auto">
        <form @submit.prevent="submitHandler" class="flex flex-col gap-6">
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

            <div class="grid gap-3" style="max-width: 200px">
              <Label for="editor">Изображение</Label>
              <ImageUploader v-model="form.preview"/>
              <InputError :message="form.errors.preview"/>
            </div>

            <div class="grid gap-3">
              <Label for="editor">Описание</Label>
              <QuillEditor id="editor" v-model="form.description"/>
              <InputError :message="form.errors.description"/>
            </div>

            <div class="grid gap-3">
              <Label for="editor">Инструкция по активации</Label>
              <QuillEditor id="editor" v-model="form.instruction"/>
              <InputError :message="form.errors.instruction"/>
            </div>

            <div>
              <Button type="submit" tabindex="6" :disabled="form.processing" class="cursor-pointer">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin"/>
                Создать
              </Button>
            </div>
          </div>
        </form>
      </div>

    </Wrapper>


  </PageLayout>
</template>
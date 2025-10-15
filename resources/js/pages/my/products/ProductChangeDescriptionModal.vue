<script setup>
import {Modal} from '@inertiaui/modal-vue'
import {ref} from "vue";
import {Label} from "@/components/ui/label/index.js";
import {LoaderCircle} from 'lucide-vue-next';
import {Button} from '@/components/ui/button/index.js'
import {useForm} from "@inertiajs/vue3";
import ErrorMessage from "@/components/shared/ErrorMessage.vue";
import {NumberField, NumberFieldContent, NumberFieldInput} from "@/components/ui/number-field/index.js";
import InputError from "@/components/ui/input/InputError.vue";
import QuillEditor from "@/components/shared/form/QuillEditor.vue";
import {showToastsFromFormData} from "@/composables/useToasts.js";

const props = defineProps({
  product: Object,
})
const form = useForm({
  description: props.product.description,
  _method: 'PATCH',
})

const modalRef = ref(null)
const submit = () => form.post(route('my.products.change.description.update', props.product.id), {
  onSuccess: (data) => {
    showToastsFromFormData(data)
    modalRef.value.close()
  }
})
</script>

<template>
  <Modal max-width="2xl" ref="modalRef">
    <div class="text-2xl font-semibold mb-8">Редактирование описания</div>

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid gap-6">
        <div class="grid gap-2">
          <Label for="editor">Описание</Label>
          <QuillEditor id="editor" v-model="form.description"/>
          <InputError :message="form.errors.description"/>
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
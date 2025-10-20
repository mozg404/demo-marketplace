<script setup>
import {Modal} from '@inertiaui/modal-vue'
import {ref} from "vue";
import {Label} from "@/components/ui/label/index.js";
import {LoaderCircle} from 'lucide-vue-next';
import {Button} from '@/components/ui/button/index.js'
import {useForm} from "@inertiajs/vue3";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import {showToastsFromFormData} from "@/composables/useToasts.js";


const props = defineProps({
  product: Object,
  features: Array,
  featureValues: Object,
})
const form = useForm({
  features: props.featureValues ?? {},
  _method: 'PATCH',
})

const modalRef = ref(null)
const submit = () => form.post(route('my.products.change.features.update', props.product.id), {
  onSuccess: (data) => {
    showToastsFromFormData(data)
    modalRef.value.close()
  }
})
</script>

<template>
  <Modal max-width="2xl" ref="modalRef" :aria-hidden="false">
    <div class="text-2xl font-semibold mb-8">Характеристики товара</div>

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid gap-6">

        <template v-for="feature in features" :key="feature.id">
          <div v-if="feature.type === 'select'" class="grid gap-3">
            <Label :for="'feature-' + feature.id">{{ feature.name }}</Label>

            <Select v-model="form.features[feature.id]">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Выберите..." />
              </SelectTrigger>
              <SelectContent
                position="popper"
                :teleport="true"
                positionStrategy="fixed"
                class="z-[1000] w-full"
              >
                <SelectItem v-for="(option,key,index) in feature.options" :key="key" :value="key">{{ option }}</SelectItem>
              </SelectContent>
            </Select>

          </div>
        </template>

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
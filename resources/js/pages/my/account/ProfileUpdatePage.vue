<script setup>

import SettingsLayout from "@/layouts/SettingsLayout.vue";
import SectionTitle from "@components/shared/layout/SectionTitle.vue";
import {useForm} from "@inertiajs/vue3";
import {Input} from "@components/ui/input/index.js";
import ErrorMessage from "@components/shared/ErrorMessage.vue";
import {Label} from "@components/ui/label/index.js";
import ProcessingButton from "@components/shared/button/ProcessingButton.vue";
import {showToasts, showToastsFromFormData} from "@/composables/useToasts.js";

const props = defineProps({
  name: String,
})

const form = useForm({
  name: props.name,
})

const submitHandler = (e) => form.patch(route('my.settings.change.profile.update'), {
  onSuccess: (data) => {
    showToastsFromFormData(data)
  },
})
</script>

<template>
  <SettingsLayout>
    <div class="max-w-xl">
      <SectionTitle class="mb-8 pt-1">Настройка профиля</SectionTitle>

      <form @submit.prevent="submitHandler" class="grid gap-6">
        <div class="grid gap-3">
          <Label for="name">Имя</Label>
          <Input id="name" type="text" v-model="form.name" required />
          <ErrorMessage :message="form.errors.name"/>
        </div>

        <div>
          <ProcessingButton class="w-auto" :is-loading="form.processing">
            Сохранить изменения
          </ProcessingButton>
        </div>
      </form>
    </div>
  </SettingsLayout>
</template>
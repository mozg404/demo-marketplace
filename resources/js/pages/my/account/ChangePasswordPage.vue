<script setup>

import SettingsLayout from "@/layouts/SettingsLayout.vue";
import SectionTitle from "@components/shared/layout/SectionTitle.vue";
import {useForm} from "@inertiajs/vue3";
import {Input} from "@components/ui/input/index.js";
import ErrorMessage from "@components/shared/ErrorMessage.vue";
import {Label} from "@components/ui/label/index.js";
import ProcessingButton from "@components/shared/button/ProcessingButton.vue";
import {showToastsFromFormData} from "@/composables/useToasts.js";

const form = useForm({
  old_password: null,
  password: null,
  password_confirmation: null,
})

const submitHandler = (e) => form.patch(route('my.settings.change.password.update'), {
  onSuccess: (data) => {
    showToastsFromFormData(data)
  },
})
</script>

<template>
  <SettingsLayout>
    <div class="max-w-xl">
      <SectionTitle class="mb-8 pt-1">Изменение пароля</SectionTitle>

      <form @submit.prevent="submitHandler" class="grid gap-6">
        <div class="grid gap-3">
          <Label for="old_password">Старый пароль</Label>
          <Input id="old_password" type="password" v-model="form.old_password" required/>
          <ErrorMessage :message="form.errors.old_password"/>
        </div>

        <div class="grid gap-3">
          <Label for="password">Новый пароль</Label>
          <Input id="password" type="password" v-model="form.password" required />
          <ErrorMessage :message="form.errors.password"/>
        </div>

        <div class="grid gap-3">
          <Label for="password_confirmation">Подтверждение пароля</Label>
          <Input id="password_confirmation" type="password" v-model="form.password_confirmation" required />
          <ErrorMessage :message="form.errors.password_confirmation"/>
        </div>

        <div>
          <ProcessingButton class="w-auto" :is-loading="form.processing">
            Изменить пароль
          </ProcessingButton>
        </div>
      </form>
    </div>
  </SettingsLayout>
</template>
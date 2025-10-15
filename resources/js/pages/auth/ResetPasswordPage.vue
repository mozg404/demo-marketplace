<script setup>

import {Card, CardContent, CardDescription, CardHeader, CardTitle} from "@components/ui/card/index.js";
import AuthLayout from "@/layouts/AuthLayout.vue";
import {Input} from "@components/ui/input/index.js";
import ErrorMessage from "@components/shared/ErrorMessage.vue";
import ProcessingButton from "@components/shared/button/ProcessingButton.vue";
import {Label} from "@components/ui/label/index.js";
import {Link, useForm} from "@inertiajs/vue3";

const props = defineProps({
  email: {
    type: String,
    required: true,
  },
  token: {
    type: String,
    required: true,
  }
})

const form = useForm({
  email: props.email,
  token: props.token,
  password: null,
  password_confirmation: null,
})
const submit = () => form.post(route('password.update'))
</script>

<template>
  <AuthLayout>
    <div class="flex w-full max-w-sm flex-col gap-8">

      <div class="flex flex-col gap-6">
        <Card>
          <CardHeader class="text-center">
            <CardTitle class="text-2xl">Сброс пароля</CardTitle>
            <CardDescription>Введите новый пароль</CardDescription>
          </CardHeader>

          <CardContent>
            <form @submit.prevent="submit">
              <div class="grid gap-6">
                <div class="grid gap-6">
                  <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" v-model="form.email" placeholder="mail@example.com" required disabled/>
                    <ErrorMessage :message="form.errors.email"/>
                  </div>

                  <div class="grid gap-2">
                    <div class="flex gap-6">
                      <div class="grid gap-2">
                        <Label for="password">Новый пароль</Label>
                        <Input id="password" type="password" v-model="form.password" required />
                      </div>

                      <div class="grid gap-2">
                        <Label for="password_confirmation">Подтверждение</Label>
                        <Input id="password_confirmation" type="password" v-model="form.password_confirmation" required />
                      </div>
                    </div>
                    <ErrorMessage :message="form.errors.password"/>
                    <ErrorMessage :message="form.errors.password_confirmation"/>
                  </div>

                  <ProcessingButton :is-loading="form.processing" class="w-full">
                    Изменить пароль
                  </ProcessingButton>
                </div>

                <div class="text-center text-sm">
                  Вспомнили пароль?
                  <Link :href="route('login')" class="underline underline-offset-4">Войти</Link>
                </div>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AuthLayout>
</template>
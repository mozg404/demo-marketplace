<script setup>

import {Card, CardContent, CardDescription, CardHeader, CardTitle} from "@components/ui/card/index.js";
import AuthLayout from "@/layouts/AuthLayout.vue";
import {Input} from "@components/ui/input/index.js";
import ErrorMessage from "@components/shared/ErrorMessage.vue";
import ProcessingButton from "@components/shared/button/ProcessingButton.vue";
import {Label} from "@components/ui/label/index.js";
import {Link, useForm} from "@inertiajs/vue3";

const form = useForm({
  email: null,
})
const submit = () => form.post(route('password.forgot.store'))
</script>

<template>
  <AuthLayout>
    <div class="flex w-full max-w-sm flex-col gap-8">

      <div class="flex flex-col gap-6">
        <Card>
          <CardHeader class="text-center">
            <CardTitle class="text-2xl">Забыли пароль?</CardTitle>
            <CardDescription>Введите свой Email для сброса</CardDescription>
          </CardHeader>

          <CardContent>
            <form @submit.prevent="submit">
              <div class="grid gap-6">
                <div class="grid gap-6">
                  <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" placeholder="mail@example.com" v-model="form.email" required/>
                    <ErrorMessage :message="form.errors.email"/>
                  </div>

                  <ProcessingButton :is-loading="form.processing" class="w-full">
                    Сбросить пароль
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
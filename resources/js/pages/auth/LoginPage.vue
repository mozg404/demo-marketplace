<script setup>
import {Link, useForm} from "@inertiajs/vue3";
import AuthLayout from "@/layouts/AuthLayout.vue";
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import ErrorMessage from "@components/shared/ErrorMessage.vue";
import ProcessingButton from "@components/shared/button/ProcessingButton.vue";
import {showToastsFromInertiaModal} from "@/composables/useToasts.js";

const form = useForm({
  name: null,
  email: null,
  password: null,
})

const submit = () => form.post(route('login.store'))
</script>

<template>
  <AuthLayout>
    <div class="flex w-full max-w-sm flex-col gap-8">

      <div class="flex flex-col gap-6">
        <Card>
          <CardHeader class="text-center">
            <CardTitle class="text-2xl">
              Вход в аккаунт
            </CardTitle>
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

                  <div class="grid gap-2">
                    <div class="flex items-center">
                      <Label html-for="password">Пароль</Label>
                      <Link :href="route('password.forgot')" class="ml-auto text-sm underline-offset-4 hover:underline">
                        Забыли пароль?
                      </Link>
                    </div>
                    <Input id="password" type="password" v-model="form.password" required />
                    <ErrorMessage :message="form.errors.password"/>
                  </div>

                  <ProcessingButton :is-loading="form.processing" class="w-full">
                    Войти
                  </ProcessingButton>
                </div>

                <div class="text-center text-sm">
                  Нет аккаунта?
                  <Link :href="route('registration')" class="underline underline-offset-4">
                    Зарегистрироваться
                  </Link>
                </div>

              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AuthLayout>
</template>

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
import ErrorMessage from "@/components/shared/ErrorMessage.vue";

const form = useForm({
  name: null,
  email: null,
  password: null,
  password_confirmation: null,
})
</script>

<template>
  <AuthLayout>
    <div class="flex w-full max-w-sm flex-col gap-8">
      <div class="flex flex-col gap-6">
        <Card>
          <CardHeader class="text-center">
            <CardTitle class="text-2xl">Регистрация</CardTitle>
          </CardHeader>

          <CardContent>
            <form>
              <div class="grid gap-6">
                <form @submit.prevent="form.post(route('registration.store'))" class="grid gap-6">
                  <div class="grid gap-2">
                    <Label for="name">Имя</Label>
                    <Input id="name" v-model="form.name" required/>
                    <ErrorMessage :message="form.errors.name"/>
                  </div>

                  <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" v-model="form.email" placeholder="mail@example.com" required/>
                    <ErrorMessage :message="form.errors.email"/>
                  </div>

                  <div class="grid gap-2">
                    <Label for="password">Пароль</Label>
                    <Input id="password" type="password" v-model="form.password" required />
                    <ErrorMessage :message="form.errors.password"/>
                  </div>

                  <div class="grid gap-2">
                    <Label for="password_confirmation">Подтверждение пароля</Label>
                    <Input id="password_confirmation" type="password" v-model="form.password_confirmation" required />
                    <ErrorMessage :message="form.errors.password_confirmation"/>
                  </div>

                  <Button type="submit" class="w-full">Зарегистрироваться</Button>
                </form>

                <div class="text-center text-sm">
                  Есть аккаунт?
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

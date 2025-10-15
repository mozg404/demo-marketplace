<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import {Card, CardContent, CardDescription, CardHeader, CardTitle} from "@components/ui/card/index.js";
import {Separator} from "@components/ui/separator";
import {Link} from "@inertiajs/vue3";
</script>

<template>
  <AuthLayout>
    <div class="flex w-full max-w-md flex-col gap-8">
      <div class="flex flex-col gap-6">
        <Card>
          <CardHeader class="text-center">
            <CardTitle class="text-2xl">
              Сброс пароля
            </CardTitle>
            <CardDescription>
              Инструкция отправлена на email
            </CardDescription>
          </CardHeader>

          <Separator/>

          <CardContent class="text-center">
            <div class="text-center text-sm">
              Не пришло письмо?
              <Link :href="route('password.forgot')" class="underline underline-offset-4">
                Отправить повторно
              </Link>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AuthLayout>
</template>
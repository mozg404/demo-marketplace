<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import logoUrl from "@img/logo.svg";
import AppHeader from "@components/modules/app/AppHeader.vue";

</script>

<template>
  <AppLayout>
    <div class="flex flex-col min-h-svh items-center justify-between gap-6 bg-muted">
      <AppHeader class="w-full bg-white"/>

      <div class="flex-1 w-full h-full flex flex-col items-center justify-center py-10">
        <slot />
      </div>

      <!--  Footer  -->
      <div></div>
    </div>
  </AppLayout>
</template>
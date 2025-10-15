<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import AppHeader from "../components/modules/app/AppHeader.vue";
import AppBreadcrumbs from "@/components/modules/app/AppBreadcrumbs.vue";
import {Separator} from "@components/ui/separator";
import AppFooter from "@components/modules/app/AppFooter.vue";

const props = defineProps({
  withBreadcrumbs: {
    type: Boolean,
    default: true,
  },
})
</script>

<template>
  <AppLayout>
    <AppHeader/>
    <Separator/>
    <AppBreadcrumbs v-if="withBreadcrumbs" />
    <slot/>
    <AppFooter/>

  </AppLayout>
</template>
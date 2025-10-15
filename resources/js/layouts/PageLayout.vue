<script setup>
import {Badge} from "@/components/ui/badge/index.js";
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import PageTitle from "@/components/shared/layout/PageTitle.vue";
import AppLayout from "@/layouts/AppLayout.vue";
import AppHeader from "@/components/modules/app/AppHeader.vue";
import AppBreadcrumbs from "@/components/modules/app/AppBreadcrumbs.vue";
import {Separator} from "@components/ui/separator/index.js";
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
    <div class="h-screen flex flex-col justify-between">
      <div>
        <AppHeader/>
        <Separator/>
        <slot name="after-header"/>
        <AppBreadcrumbs v-if="withBreadcrumbs" />

        <div class="py-12">
          <Wrapper v-if="$slots.title">
            <div class="mb-12">

              <div class="flex justify-between items-center">
                <div class="flex items-center">
                  <PageTitle v-if="$slots.title" >
                    <slot name="title"/>
                  </PageTitle>

                  <Badge v-if="$slots.counter" variant="secondary" class="ml-4 py-1.5 px-2.5">
                    <slot name="counter"/>
                  </Badge>
                </div>

                <div class="flex items-center space-x-4" v-if="$slots.actions">
                  <slot name="actions"/>
                </div>
              </div>

            </div>
          </Wrapper>
          <slot/>
        </div>
      </div>

      <AppFooter/>
    </div>
  </AppLayout>
</template>
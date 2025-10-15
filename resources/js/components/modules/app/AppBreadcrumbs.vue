<script setup>
import {Link, usePage} from '@inertiajs/vue3'
import {computed} from "vue";
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/components/ui/breadcrumb/index.js'
import Wrapper from "@/components/shared/layout/Wrapper.vue";

const page = usePage()
const breadcrumbs = computed(() => page.props.breadcrumbs)
</script>

<template>
  <div class="pt-3" v-if="breadcrumbs.length > 0">
    <Wrapper>
      <Breadcrumb>
        <BreadcrumbList>

          <template v-for="(item, index) in breadcrumbs" :key="index">
            <template v-if="(index + 1) < breadcrumbs.length ">
              <BreadcrumbItem>
                <BreadcrumbLink as-child>
                  <Link :href="item.url">{{ item.title }}</Link>
                </BreadcrumbLink>
              </BreadcrumbItem>
              <BreadcrumbSeparator />
            </template>
            <template v-else>
              <BreadcrumbItem>
                <BreadcrumbPage>{{ item.title }}</BreadcrumbPage>
              </BreadcrumbItem>
            </template>
          </template>

        </BreadcrumbList>
      </Breadcrumb>
    </Wrapper>
  </div>
</template>
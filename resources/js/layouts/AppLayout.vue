<script setup>
import {Toaster} from 'vue-sonner'
import 'vue-sonner/style.css'
import {Head, usePage} from "@inertiajs/vue3";
import {watch} from "vue";
import PageLoaderWrapper from "@/components/shared/PageLoaderWrapper.vue";
import {showToasts} from "@/composables/useToasts.js";

const {seo, toasts} = usePage().props;

watch(
  () => toasts,
  (toasts) => showToasts(toasts),
  {
    deep: true,
    immediate: true
  }
)
</script>

<template>
  <Toaster closeButton richColors position="top-right"/>

  <Head>
    <title>{{ seo.title }}</title>
    <meta name="description" :content="seo.description">

    <!-- Open Graph -->
    <meta property="og:title" :content="seo.openGraph.title" />
    <meta property="og:description" :content="seo.openGraph.description" />
    <meta property="og:image" :content="seo.openGraph.image" />
    <meta property="og:url" :content="seo.openGraph.url" />
    <meta property="og:type" :content="seo.openGraph.type" />
    <meta property="og:site_name" :content="seo.openGraph.site_name" />

    <!-- Twitter Card -->
    <meta name="twitter:card" :content="seo.twitterCard.card" />
    <meta name="twitter:title" :content="seo.twitterCard.title" />
    <meta name="twitter:description" :content="seo.twitterCard.description" />
    <meta name="twitter:image" :content="seo.twitterCard.image" />
    <meta
      v-if="seo.twitterCard.site"
      name="twitter:site"
      :content="seo.twitterCard.site"
    />
  </Head>

  <PageLoaderWrapper>
    <div class="min-h-screen">
      <slot/>
    </div>
  </PageLoaderWrapper>
</template>
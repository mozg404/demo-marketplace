<script setup>
import {Card, CardContent, CardHeader, CardTitle} from "@/components/ui/card/index.js";
import {cn} from "@/lib/utils.js";

const props = defineProps({
  title: {
    type: String,
    required: false,
    default: null,
  },
  class: {
    type: String,
    required: false,
    default: null,
  }
})
</script>

<template>
  <Card :class="cn(props.class)">
    <CardHeader v-if="title">
      <CardTitle>{{ title }}</CardTitle>
    </CardHeader>
    <CardContent>
      <slot/>
    </CardContent>
  </Card>
</template>
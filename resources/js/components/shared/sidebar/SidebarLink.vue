<script setup>

import {Button} from "@components/ui/button/index.js";
import {cn} from "@/lib/utils.js";

const props = defineProps({
  class: {
    type: String,
    default: ""
  },
  active: Boolean,
  asChild: { type: Boolean, required: false },
})
</script>

<template>
  <Button
    variant="ghost"
    :as-child="asChild"
    :class="cn('w-full justify-start h-10 text-muted-foreground transition-colors', (active ? 'bg-accent text-accent-foreground hover:text-accent-foreground' : ''), props.class)"
  >
    <slot/>
  </Button>
</template>

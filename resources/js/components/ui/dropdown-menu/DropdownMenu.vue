<script setup>
import { DropdownMenuRoot, useForwardPropsEmits } from 'reka-ui';
import {provide, ref} from "vue";

const props = defineProps({
  defaultOpen: { type: Boolean, required: false },
  open: { type: Boolean, required: false },
  dir: { type: String, required: false },
  modal: { type: Boolean, required: false },
});
const emits = defineEmits(['update:open']);

const forwarded = useForwardPropsEmits(props, emits);

// (!!!) Передаем состояние для дочерок, чтобы закрывать автоматом при модалках
const dropdownOpen = ref(false)
provide('dropdownOpen', dropdownOpen)
</script>

<template>
  <DropdownMenuRoot data-slot="dropdown-menu" v-bind="forwarded">
    <slot />
  </DropdownMenuRoot>
</template>

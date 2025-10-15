<script setup>
import { DropdownMenuItem } from '@/components/ui/dropdown-menu'
import { visitModal } from '@inertiaui/modal-vue'
import { inject } from 'vue'
import {cn} from "@/lib/utils.js";

const props = defineProps({
  href: {
    type: String,
    required: true
  },
  class: String,
})

const dropdownOpen = inject('dropdownOpen')

function handleClick() {
  dropdownOpen.value = false
  setTimeout(() => visitModal(props.href), 100)
}
</script>

<template>
  <DropdownMenuItem as-child>
    <button @click="handleClick" :class="cn('w-full text-left block cursor-pointer', props.class)">
      <slot />
    </button>
  </DropdownMenuItem>
</template>
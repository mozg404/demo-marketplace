<script setup>
import { useForm } from '@inertiajs/vue3';
import Wrapper from "@/components/shared/layout/Wrapper.vue";
import CategorySelect from "@/components/modules/products/CategorySelect.vue";
import {Label} from "@/components/ui/label/index.js";
import ErrorMessage from "@/components/shared/ErrorMessage.vue";
import { ref } from "vue"

import { Button } from "@/components/ui/button"
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from "@/components/ui/command"
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"

const statuses = [
  {
    value: "backlog",
    label: "Backlog",
  },
  {
    value: "todo",
    label: "Todo",
  },
  {
    value: "in progress",
    label: "In Progress",
  },
  {
    value: "done",
    label: "Done",
  },
  {
    value: "canceled",
    label: "Canceled",
  },
]

const open = ref(false)
const selectedStatus = ref(null)
</script>

<template>
  <Wrapper class="mt-10 p-10 flex relative">

    <CategorySelect/>

  </Wrapper>
</template>

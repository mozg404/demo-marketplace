<script setup>
import MultipleCheckbox from "@/components/shared/form/MultipleCheckbox.vue";
import { ScrollArea } from '@/components/ui/scroll-area'
import {Badge} from "@/components/ui/badge/index.js";

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({}),
  },
  features: {
    type: [Array, null],
  },
})

const emit = defineEmits(['update:modelValue']);

function updateFeatureSelection(featureId, selectedValues) {
  const updated = { ...props.modelValue };

  if (selectedValues.length > 0) {
    updated[featureId] = selectedValues;
  } else {
    delete updated[featureId];
  }

  emit('update:modelValue', updated);
}

function getCurrentValues(featureId) {
  return props.modelValue[featureId] || [];
}
</script>

<template>
  <template v-for="feature in features" :key="feature.id">
    <div v-if="feature.type === 'select'" class="grid gap-2 mr-4">

      <div class="flex justify-between items-center">
        <div class="text-sm leading-none font-medium mb-1">
          {{feature.name}}
        </div>
        <Badge class="rounded-full w-5 h-5 -mr-2" v-if="getCurrentValues(feature.id).length > 0" variant="secondary">{{ getCurrentValues(feature.id).length }}</Badge>
      </div>

      <ScrollArea class="max-h-[210px] ">
        <div class="space-y-1">
          <MultipleCheckbox
            v-for="(label, value) in feature.options"
            :key="value"
            :modelValue="getCurrentValues(feature.id)"
            @update:modelValue="updateFeatureSelection(feature.id, $event)"
            :value="value"
            :label="label"
            label-class="transition-colors duration-200 text-muted-foreground font-normal text-sm"
            label-active-class="text-accent-foreground"
          />
        </div>
      </ScrollArea>
    </div>
  </template>
</template>

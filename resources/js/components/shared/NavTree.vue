<script setup>
import { ref } from 'vue'
import { ChevronDown, ChevronRight, Folder, FolderOpen, File } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Collapsible, CollapsibleTrigger, CollapsibleContent } from '@/components/ui/collapsible'
import {Link} from "@inertiajs/vue3";

const props = defineProps({
  items: {
    type: Array,
    required: true,
    default: () => []
  }
})

const openItems = ref({})

const toggleItem = (id) => {
  openItems.value[id] = !openItems.value[id]
}
</script>

<template>
  <div class="w-full space-y-1">
    <template v-for="item in items" :key="item.id">
      <div v-if="item.children">
        <Collapsible :open="openItems[item.id]" @update:open="openItems[item.id] = $event">
          <div class="flex items-center space-x-1">
            <CollapsibleTrigger as-child>
              <Button
                variant="ghost"
                size="sm"
                class="w-full justify-start font-normal"
                @click="toggleItem(item.id)"
              >
                <component
                  :is="openItems[item.id] ? ChevronDown : ChevronRight"
                  class="h-4 w-4 mr-2"
                />
                <component
                  :is="openItems[item.id] ? FolderOpen : Folder"
                  class="h-4 w-4 mr-2 text-accent-foreground/70"
                />
                <span>{{ item.name }}</span>
              </Button>
            </CollapsibleTrigger>
          </div>

          <CollapsibleContent class="pl-6 mt-1">
            <NavTree :items="item.children" />
          </CollapsibleContent>
        </Collapsible>
      </div>

      <Button
        v-else
        variant="ghost"
        size="sm"
        class="w-full justify-start font-normal"
        :class="{ 'bg-accent': $route.path === item.href }"
        as-child
      >
        <Link herf="/" class="flex items-center w-full">
          <File class="h-4 w-4 mr-2 text-accent-foreground/50" />
          <span>{{ item.name }}</span>
        </Link>
      </Button>
    </template>
  </div>
</template>
import { computed, useAttrs, inject } from 'vue'
import {generateUniqueId} from "@/lib/support.js";

export function useFieldId(props, prefix = 'field') {
    const attrs = useAttrs()
    const contextId = inject('fieldId', null)

    return computed(() => {
        return props.id || contextId?.value || attrs.id || generateUniqueId(prefix)
    })
}
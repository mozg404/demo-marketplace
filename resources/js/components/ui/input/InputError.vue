<script setup>
    defineProps({
        message: String,
    })
</script>

<template>
    <div v-show="message">
        <p class="text-xs text-red-600 dark:text-red-500">
            {{ message }}
        </p>
    </div>
</template>

<script setup>
    defineProps({
        message: String,
    })
</script>

<template>
    <div v-show="message">
        <p class="text-xs text-destructive whitespace-normal">
            {{ message }}
        </p>
    </div>
</template>

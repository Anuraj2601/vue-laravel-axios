<template>
    <div class="message">
        <p>{{ message }}</p>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    const message = ref("Hello from Vue!");
</script>

<style scoped>
.message {
    color: green;
    text-align: center;
    font-size: 24px;
}
</style>

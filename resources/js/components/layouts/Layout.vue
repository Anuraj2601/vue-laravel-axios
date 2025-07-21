<template>
  <div class="flex flex-col h-screen overflow-hidden">
    <Navbar />

    <div class="flex flex-1 overflow-hidden">
      <Sidebar />

      <main class="flex-1 p-6 overflow-y-auto">
        <router-view />
      </main>
    </div>
  </div>
</template>



<script setup>
import Sidebar from './Sidebar.vue';
import Navbar from './Navbar.vue';
</script>

<style scoped>


</style>

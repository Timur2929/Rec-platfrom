<template>
  <div class="page-container">
    <Sidebar :user="$page.props.auth.user" />
    <div class="main-content">
      <slot />
    </div>
  </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue';
</script>

<style>
.page-container {
  display: flex;
  min-height: 100vh;
  background-color: #fff;
}

.main-content {
  flex: 1;
  margin-left: 250px;
  padding: 40px;
}
</style> 
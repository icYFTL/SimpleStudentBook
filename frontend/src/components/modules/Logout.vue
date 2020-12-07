<template>
  <div></div>
</template>

<script>
import deleteAllCookies from "@/utils";

export default {
  name: "Logout",
  beforeCreate() {
    deleteAllCookies();
    this.$router.push('/login');
  }
}
</script>

<style scoped>

</style>
<template>
  <div class="s-field">
    <img src="../../../assets/empty_avatar.png">
    <a>{{ user.name || 'nil' }}<br>{{ user.surname || 'nil' }}</a> <!-- PREVIEW -->
  </div>
</template>

<script>
export default {
  name: "StudentField",
  data() {
    return {
      user: {
        'name': 'none',
        'surname': 'none'
      }
    }
  },
  created() {
    this.getUser();
  },
  methods: {
    getUser: function () {
        this.$store.dispatch('getUser').then(() => {
        this.user = this.$store.state.user;
      });
    }
  }
}
</script>

<style scoped>
.s-field {
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
}

.s-field img {
  width: 100px;
  height: 100px;
}

.s-field a {
  margin-top: 0.5em;
}
</style>
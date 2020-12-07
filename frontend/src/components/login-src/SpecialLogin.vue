<template>
  <div class="col-md-6 col-sm-12">
    <div class="special-login-form">
      <form @submit.prevent="auth">
        <div class="form-group">
          <input required type="text" class="form-control" placeholder="Username" v-model="login">
        </div>
        <div class="form-group">
          <input required type="password" class="form-control" placeholder="Password" v-model="password">
        </div>
        <button type="submit" class="btn btn-black">Submit</button>
      </form>
    </div>
  </div>
</template>

<script>
import deleteAllCookies from "@/utils";

export default {
  name: "SpecialLogin",
  data() {
    return {
      login: '',
      password: ''
    }
  },
  methods: {
    auth: function () {
      this.$store.dispatch('login', {
        'username': this.login,
        'password': this.password,
        'auth_type': 'special'
      }).then(() => {
            if (this.$store.getters.isLoggedIn) {
              this.$router.push('/');
            } else if (this.$store.state.error) {
              this.$toast.error(this.$store.state.error);
              deleteAllCookies();
            } else
              throw new Error('EVERYTHING IS BAD');
          })
          .catch((err) => {
            this.$toast.warning('Something went wrong. Try to reload the page.')
            deleteAllCookies();
            console.log(err)
          })
    }
  }
}

</script>

<style scoped>
.btn-black {
  background-color: #000 !important;
  color: #fff !important;
  width: 50%;
}

</style>
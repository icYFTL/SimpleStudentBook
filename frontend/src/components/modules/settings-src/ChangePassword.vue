<template>
  <div class="change-password">
    <h3>Смена пароля</h3>
    <input type="password" placeholder="Новый пароль" v-model="password">
    <input type="password" placeholder="Повторите пароль" v-model="retype_password">
    <button class="btn btn-primary" v-on:click="changePassword">Применить</button>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ChangePassword",
  data() {
    return {
      'password': '',
      'retype_password': ''
    }
  },
  methods:{
    changePassword: async function (){
      if (this.password !== this.retype_password) {
        this.$toast.error('Пароли не сходятся');
        return false;
      }

      if (this.password.length < 7 || this.password.length > 30){
        this.$toast.error('Неверная длина пароля');
        return false;
      }

      await axios({
        url: this.$store.state.api_host,
        data: {'method': 'updatePassword', 'password': this.password},
        withCredentials: true,
        method: 'POST'
      }).then((response) => {
        if (response.data.status){
          this.$toast.success('Успешно применено!');
          this.password = '';
          this.retype_password = '';
        }
        else
          this.$toast.error(response.data.error);
      }).catch((err) => {
        this.$toast.error(err.toString());
      })
    }
  }
}
</script>

<style lang="scss" scoped>

$block-width: 400px;

.change-password{
  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
  width: 400px;
  height: 200px;
  border-radius: 10px;
  border: black solid 1px;
  @media (max-width: 400px) {
    width: 200px;
  }
}

.change-password input {
  width: 300px;
  margin-left: $block-width/9;
  @media (max-width: 400px) {
    width: 200px;
    margin-left: 0;
  }
}

.change-password button{
  width: 200px;
  margin-left: $block-width/4;
  margin-top: 5px;
  @media (max-width: 400px) {
    width: 150px;
    margin-left: 0;
  }
}


</style>
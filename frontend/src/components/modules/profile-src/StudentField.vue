<template>
  <div class="s-field">
    <img src="../../../assets/empty_avatar.png">
    <div v-if="!editable">
      <a v-if="user.name && user.surname">{{ user.name}}<br>{{ user.surname}}</a>
      <a v-else>{{ user.username }}</a>
    </div>
    <div v-if="editable" class="snp-mutable">
      <input type="text" v-model="userName" placeholder="Name">
      <input type="text" v-model="userSurName" placeholder="Surname">
      <input type="text" v-model="userPatronymic" placeholder="Patronymic">
      <button v-if="snpChanged" v-on:click="applyChanges" class="btn btn-primary">Apply</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "StudentField",
  props: {
    editable: Boolean || false
  },
  data() {
    return {
      user: {
        'name': 'none',
        'surname': 'none'
      },
      userName: null,
      userSurName: null,
      userPatronymic: null,
      snpChanged: false
    }
  },
  created() {
    this.getUser();
  },
  methods: {
    getUser: function () {
      this.$store.dispatch('getUser').then(() => {
        this.user = this.$store.state.user;
        this.userName = this.user.name;
        this.userSurName = this.user.surname;
        this.userPatronymic = this.user.patronymic;
      });
    },
    isSnpChanged: function () {
      this.snpChanged = this.userName !== this.user.name && this.userSurName !== this.user.surname && this.userPatronymic !== this.user.patronymic;
    },
    applyChanges: async function () {
      await axios({
        url: this.$store.state.api_host,
        data: {
          'method': 'updateSNP',
          'name': this.userName,
          'surname': this.userSurName,
          'patronymic': this.userPatronymic
        },
        withCredentials: true,
        method: 'POST'
      }).then((response) => {
        if (response.data.status) {
          this.$toast.success('Successfully changed.');
          this.$store.state.user.name = this.userName;
          this.$store.state.user.surname = this.userSurName;
          this.$store.state.user.patronymic = this.userPatronymic;
          this.$forceUpdate();
        } else
          this.$toast.error(response.data.error);

      }).catch((err) => {
        this.$toast.error(err);
      })
    }
  },
  watch: {
    userName: function () {
      this.isSnpChanged();
    },
    userSurName: function () {
      this.isSnpChanged();
    },
    userPatronymic: function () {
      this.isSnpChanged();
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

.snp-mutable {
  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
}

.snp-mutable input {
  background: inherit;
  outline: none;
  border-radius: 10px;
  color: whitesmoke;
  text-align: center;
  margin-top: 5px;
}

.snp-mutable button {
  margin-top: 10px;
}

</style>
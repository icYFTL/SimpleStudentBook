import Vue from 'vue'
import App from './App.vue'
import {BootstrapVue} from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import Vuex from 'vuex'
import Axios from 'axios'
import router from "./router";
import Router from "vue-router";
import store from "./store"
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import VueCookies from 'vue-cookies'


Vue.use(Vuex)
Vue.use(Axios)
Vue.use(BootstrapVue)
//Vue.use(IconsPlugin)
Vue.use(Toast)
Vue.use(Router)
Vue.use(VueCookies)

Vue.config.productionTip = false
Vue.prototype.$http = Axios;


new Vue({
    render: h => h(App),
    router,
    store
}).$mount('#app')

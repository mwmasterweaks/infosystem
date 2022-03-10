import Vue from "vue";
import App from "./App.vue";
import Router from "./routes.js";
import VueResource from "vue-resource";
import VeeValidate from "vee-validate";
import VueRangedatePicker from "vue-rangedate-picker";
import Auth from "./packages/auth/Auth.js";
import Global from "./packages/local/Global.js";
import { BootstrapVue, IconsPlugin } from "bootstrap-vue";
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";
import VueGeolocation from "vue-browser-geolocation";
import * as VueGoogleMaps from "vue2-google-maps";
import vuetify from "./plugins/js/vuetify.js";

//import excel from "vue-excel-export";

//import DateRangePicker from "vue2-daterange-picker";

Vue.use(VueGoogleMaps, {
  load: {
    key: "AIzaSyCCvHdNEai5B7i5fXOhgl0nvz_eYLBn_tE"
  }
});
Vue.use(VueGeolocation);
Vue.use(VueRangedatePicker);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VueResource);
Vue.use(Auth);
Vue.use(Global);
Vue.use(VeeValidate, {
  fieldsBagName: "veeFields"
});
var protocol = window.location.protocol;

console.log(protocol);
if (window.location.host == "inetinfosystem.dctechmicro.com") {
  Vue.http.options.root = protocol + "//inetinfosystem.dctechmicro.com/back/";
  Vue.prototype.$img_path =
    protocol + "//inetinfosystem.dctechmicro.com/back/public/imgs/";
  Vue.prototype.$attachment_path =
    protocol + "//inetinfosystem.dctechmicro.com/back/public/attachments/";
} else if (window.location.host == "dcinfosystem.dctechmicro.com") {
  Vue.http.options.root = protocol + "//dcinfosystem.dctechmicro.com/back/";
  Vue.prototype.$img_path =
    protocol + "//dcinfosystem.dctechmicro.com/back/public/imgs/";
  Vue.prototype.$attachment_path =
    protocol + "//dcinfosystem.dctechmicro.com/back/public/attachments/";
} else if (window.location.host == "infosystem-sandbox.dctechmicro.com") {
  Vue.http.options.root =
    protocol + "//infosystem-sandbox.dctechmicro.com/back/";
  Vue.prototype.$img_path =
    protocol + "//infosystem-sandbox.dctechmicro.com/back/public/imgs/";
  Vue.prototype.$attachment_path =
    protocol + "//infosystem-sandbox.dctechmicro.com/back/public/attachments/";
} else if (window.location.host == "localhost:8080") {
  Vue.http.options.root = protocol + "//localhost:8000";
  Vue.prototype.$img_path = protocol + "//localhost:8000/imgs/";
  Vue.prototype.$attachment_path = protocol + "//localhost:8000/attachments/";
}
// else if (window.location.host == "localhost:8080") {
//   Vue.http.options.root = "http://localhost:8000";
//   Vue.prototype.$img_path = "http://localhost:8000/imgs/";
//   Vue.prototype.$attachment_path = "http://localhost:8000/attachments/";
// }

Vue.http.headers.common["Authorization"] = "Bearer " + Vue.auth.getToken();

// fetch("imgPath.txt")
//   .then(response => response.text())
//   .then(text => (Vue.prototype.$img_path = text));

Router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.forVisitors)) {
    if (Vue.auth.isAuthenticated()) {
      next({
        path: "/home"
      });
    } else next();
  } else if (to.matched.some(record => record.meta.forAuth)) {
    if (!Vue.auth.isAuthenticated()) {
      next({
        path: "/login"
      });
    } else next();
  } else next();
});

new Vue({
  vuetify,
  render: h => h(App),
  router: Router
}).$mount("#app");

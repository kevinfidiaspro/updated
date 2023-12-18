require("./bootstrap");
window.Vue = require("vue");

import vuetify from "./vuetify_installer/vuetify_installer";
import Main from "./base_components/main/Main.vue";
import router from "./router/router.js";
import auth from "./auth/auth.js";
import store from "./store/store.js";
import "@mdi/font/css/materialdesignicons.css";
import CKEditor from "@ckeditor/ckeditor5-vue2";
Vue.use(CKEditor);

const app = new Vue({
    el: "#app",
    vuetify,
    router,
    store,
    icons: {
        iconfont: "mdi",
    },
    components: {
        "main-component": Main,
    },
    mounted() {
        if (auth.check()) {
            this.setUser();
        }
    },
    methods: {
        setUser() {
            store.dispatch("setUser", {
                name: localStorage.getItem("user_name"),
                role: localStorage.getItem("role"),
                rol_tfg: localStorage.getItem("rol_tfg"),
                email: localStorage.getItem("user_email"),
                id: localStorage.getItem("user_id"),
            });
            store.dispatch("setAuth", true);
        },
    },
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

/*const files = require.context("./base_components/base", true, /\.vue$/i);
files.keys().map(key =>
    Vue.component(
        key           .split("/")
            .pop()
            .split(".")[0],
        files(key).default
    )
);*/

/*const files = require.context('./global_components', true, /\.vue$/i);
files.keys().map(key => {
  console.log(key.split('/').pop().split('.')[0]);
  Vue.component(key.split('/').pop().split('.')[0], files(key).default)
})*/

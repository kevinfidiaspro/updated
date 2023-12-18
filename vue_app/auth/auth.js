import router from "../router/router.js";
import store from "../store/store.js";
import Vue from "vue";

export default {
    user: {
        authenticated: false,
    },

    signin: function (user) {
        axios
            .post("api/login", user)
            .then((response) => {
                this.setLocalStorage(response.data);
                this.dispatchUser(response.data.user);
                this.user.authenticated = true;
                axios.defaults.headers.common["Authorization"] =
                    "Bearer " + response.data.token;
                window.location.href = "/";
                //  router.push('/')
            })
            .catch((error) => {
                console.log(error);
                //this.$toast.error('Acceso denegado')
            });
    },

    dispatchUser: function (data) {
        store.dispatch("setAuth", true);
        store.dispatch("setUser", data);
    },

    setLocalStorage: function (data) {
        const user_id = localStorage.getItem("user_id");
        if (data.user.role == 1 && data.user.id != user_id && user_id != null)
            return;
        localStorage.setItem("id_token", data.token);
        localStorage.setItem("user_name", data.user.name);
        localStorage.setItem("user_email", data.user.email);
        localStorage.setItem("role", data.user.role);
        localStorage.setItem("rol_tfg", data.user.rol_tfg);
        localStorage.setItem("user_id", data.user.id);
        localStorage.setItem("real_role", data.user.role);
        localStorage.setItem("real_user_id", data.user.id);
    },

    logout: function () {
        axios
            .post("api/logout")
            .then((response) => {
                localStorage.removeItem("id_token");
                localStorage.removeItem("user_name");
                localStorage.removeItem("user_email");
                localStorage.removeItem("role");
                localStorage.removeItem("user_id");
                store.dispatch("setAuth", false);
                store.dispatch("setUser", this.setDefault());
                router.push("/login");
            })
            .catch((error) => {
                console.log(error);
            });
    },

    setDefault: function () {
        return {
            name: "...",
        };
    },

    authenticated: function () {
        return this.check();
    },

    check: function () {
        return localStorage.getItem("id_token") !== null ? true : false;
    },
};

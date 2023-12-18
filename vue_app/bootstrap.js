window.Vue = require("vue");

import VuetifyToast from "vuetify-toast-snackbar-ng";

import setup from "./interceptors/interceptors.js";
setup();

import { VueMaskDirective } from "v-mask";
Vue.directive("mask", VueMaskDirective);

import VueRouter from "vue-router";
Vue.use(VueRouter);

import Loader from "./base_components/base/Loader.vue";
Vue.component("loader", Loader);

import VCustomTitle from "./global_components/VCustomTitle.vue";
Vue.component("v-custom-title", VCustomTitle);

import VCustomCard from "./global_components/VCustomCard.vue";
Vue.component("v-custom-card", VCustomCard);

import VClienteComponent from "./global_components/VClienteComponent.vue";
Vue.component("v-cliente-component", VClienteComponent);

import VCustomMenuCalendar from "./global_components/VCustomMenuCalendar.vue";
Vue.component("v-custom-menu-calendar", VCustomMenuCalendar);

import VConfirmDialog from "./global_components/VConfirmDialog.vue";
Vue.component("v-confirm-dialog", VConfirmDialog);

import Nl2br from "vue-nl2br";
Vue.component("nl2br", Nl2br);

import dynamic_select from "./components/general/dynamicSelect.vue";
Vue.component("dynamic_select", dynamic_select);
import cita_picker from "./components/general/planpicker/citapicker.vue";
Vue.component("cita-picker", cita_picker);

import dateSelect from "./components/general/dateSelect.vue";
Vue.component("date-select", dateSelect);
window.axios = require("axios");

axios.defaults.headers.common["Content-Type"] = "application/json";
axios.defaults.headers.common["Content-Type"] = "application/json";

axios.defaults.headers.common["Cache-Control"] = "no-cache";
axios.defaults.headers.common["Vary"] = "*";

axios.defaults.headers.common.Authorization = `Bearer ${localStorage.getItem(
    "id_token"
)}`;
axios.defaults.headers.common.user_id = `${localStorage.getItem("user_id")}`;
axios.defaults.withCredentials = true;
Vue.filter("format_date", function (value) {
    let str = (value + "T" + " ").split("T")[0].split(" ")[0].split("-");
    return `${str[2]}-${str[1]}-${str[0]}`;
});
Vue.filter("hora_formated", function (val) {
    if (val == null) return "";

    let str = val.toString().split(".");
    if (str.length > 1) {
        return str[0].toString().padStart(2, "0") + ":30";
    }
    return val.toString().padStart(2, "0") + ":00";
});
Vue.use(VuetifyToast, {
    x: "right",
    y: "top",
    color: "info",
    icon: "mdi-info",
    timeout: 3000,
    dismissable: true,
    autoHeight: false,
    multiLine: false,
    vertical: false,
    shorts: {
        error: {
            color: "red",
        },
        sucs: {
            color: "green",
        },
        warn: {
            color: "orange",
        },
    },
    property: "$toast",
});

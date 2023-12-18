import modulo_estado from "./modulos/modulo_estado";
import modulo_provincias from "./modulos/modulo_provincias";
import modulo_estados from "./modulos/modulo_estados";
import modulo_servicios from "./modulos/modulo_servicios";
import modulo_filtros from "./modulos/modulo_filtros";
import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

const store = new Vuex.Store({
    strict: false,

    modules: {
        estado: modulo_estado,
        provincias: modulo_provincias,
        estados: modulo_estados,
        servicios: modulo_servicios,
        filtros: modulo_filtros,
    },
});

export default store;

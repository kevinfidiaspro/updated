import ListaGastosTfg from "../componentes/ListaGastosTfg.vue";
import FormGastosTfg from "../componentes/FormGastosTfg.vue";

const routes = [
    ...route("/lista-gastos-tfg", ListaGastosTfg, {
        Auth: true,
        //req_admin: true
    }),
];

function route(path, component = Default, meta = {}) {
    return [
        {
            path,
            component,
            meta,
        },
    ];
}

export default routes;

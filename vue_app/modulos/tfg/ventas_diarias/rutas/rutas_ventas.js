import ListaVentasTfg from "../componentes/ListaVentasTfg.vue";
import ListaResumen from "../componentes/ListaResumenVentas.vue";

const routes = [
    ...route("/lista-ventas-tfg", ListaVentasTfg, {
        Auth: true,
        //req_admin: true
    }),
    ...route("/lista-ventas-resumen-tfg", ListaResumen, {
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

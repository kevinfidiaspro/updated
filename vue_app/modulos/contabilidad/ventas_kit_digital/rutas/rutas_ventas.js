import ListaVentas from "../componentes/ListaVentas.vue";
import ListaResumen from "../componentes/ListaResumenVentas.vue";

const routes = [
    ...route("/lista-ventas-kit", ListaVentas, {
        Auth: true,
        //req_admin: true
    }),
    ...route("/lista-ventas-kit-resumen", ListaResumen, {
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

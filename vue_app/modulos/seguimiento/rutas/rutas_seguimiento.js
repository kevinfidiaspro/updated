import ListaSeguimiento from "../componentes/ListaSeguimiento.vue";
import CalendarioSeguimiento from "../componentes/CalendarioSeguimiento.vue";
import FormSeguimiento from "../componentes/FormSeguimiento.vue";

const routes = [
    ...route("/guardar-seguimiento", FormSeguimiento, {
        Auth: true,
        potencial: true,
        ///req_admin: true
    }),
    ...route("/lista-seguimiento", ListaSeguimiento, {
        Auth: true,
        potencial: true,

        //req_admin: true
    }),
    ...route("/calendario-seguimiento", CalendarioSeguimiento, {
        Auth: true,
        potencial: true,

        //req_admin: true
    }),
    ...route("/lista-seguimiento-cliente", ListaSeguimiento, {
        Auth: true,
        potencial: false,

        //req_admin: true
    }),
    ...route("/calendario-seguimiento-cliente", CalendarioSeguimiento, {
        Auth: true,
        potencial: false,

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

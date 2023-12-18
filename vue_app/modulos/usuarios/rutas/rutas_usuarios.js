import FormUsuarios from "../componentes/FormUsuarios.vue";
import ListaUsuarios from "../componentes/ListaUsuarios.vue";
import CalendarioVacaciones from "../componentes/CalendarioVacaciones.vue";

const routes = [
    ...route("/guardar-usuario", FormUsuarios, {
        Auth: true,
        //req_admin: true
    }),
    ...route("/lista-usuario", ListaUsuarios, {
        Auth: true,
        req_admin: true,
    }),
    ...route("/calendario-vacaciones", CalendarioVacaciones, {
        Auth: true,
        //req_admin: false,
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

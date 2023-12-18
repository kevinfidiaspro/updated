import ListaMarketing from "../componentes/ListaMarketing.vue";

const routes = [
    ...route("/lista-marketing", ListaMarketing, {
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

import FormFormularios from "../componentes/FormFormularios.vue";
import ListaFormularios from "../componentes/ListaFormularios.vue";
import FormPagina from "../componentes/FormPagina.vue";
import ListaLeads from "../componentes/ListaLeads";
import ListaPaginas from "../componentes/ListaPaginas.vue";
import Campanas from "../componentes/Campanas.vue";

const routes = [
    ...route("/guardar-formulario-facebook", FormFormularios, {
        Auth: true,
        //req_admin: true,
    }),
    ...route("/guardar-pagina-facebook", FormPagina, {
        Auth: true,
        //req_admin: true,
    }),
    ...route("/lista-formulario-facebook", ListaFormularios, {
        Auth: true,
    }),
    ...route("/lista-paginas-facebook", ListaPaginas, {
        Auth: true,
    }),
    ...route("/lista-leads-facebook", ListaLeads, {
        Auth: true,
    }),
    ...route("/lista-campanas", Campanas),
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

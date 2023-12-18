import FormPotenciales from "../componentes/FormPotenciales";
import ListaPotenciales from "../componentes/ListaPotenciales";

const routes = [
    ...route("/guardar-potencial", FormPotenciales, {
        Auth: true,
        titulo: "Potencial",
        estado: 3,
        listado: "lista-potenciales",
    }),
    ...route("/guardar-cliente", FormPotenciales, {
        Auth: true,
        titulo: "Cliente",
        estado: 2,
        listado: "lista-clientes",
    }),
    ...route(`/lista-potenciales`, ListaPotenciales, {
        Auth: true,
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

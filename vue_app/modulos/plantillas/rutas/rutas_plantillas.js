import ListaPlantillas from "../componentes/ListaPlantillas.vue";
import FormPlantillas from "../componentes/FormPlantillas.vue";

const routes = [
    ...route("/guardar-plantilla", FormPlantillas, {
        Auth: true,
        ///req_admin: true
    }),
    ...route("/lista-plantilla", ListaPlantillas, {
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

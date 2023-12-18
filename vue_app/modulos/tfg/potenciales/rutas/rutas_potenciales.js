import ListaPotencialesTfg from "../componentes/ListaPotencialesTfg.vue";
import ResumenPotencialesTfg from "../componentes/ResumenPotencialesTfg.vue";

const routes = [
    ...route("/lista-potenciales-tfg", ListaPotencialesTfg, {
        Auth: true,
        //req_admin: true
    }),
    ...route("/resumen-potenciales-tfg", ResumenPotencialesTfg, {
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

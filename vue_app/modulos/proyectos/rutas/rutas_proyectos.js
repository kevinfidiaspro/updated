import FormProyecto from "../componentes/FormProyecto";
import ListaProyectos from "../componentes/ListaProyectos";
import ListaClientes from "../componentes/ListaClientes";
import RegistrarPresupuesto from "../componentes/RegistrarPresupuesto";
import ListaProyectosAll from "../componentes/ListaProyectosAll";
import ListaConsumoHora from "../componentes/ListaConsumoHoras";
const routes = [
    ...route("/consumo-proyecto", ListaConsumoHora, {
        Auth: true,
    }),
    ...route("/guardar-proyecto", FormProyecto, {
        Auth: true,
        to: "lista-proyectos",
    }),
    ...route("/editar-proyecto", FormProyecto, {
        Auth: true,
        to: "lista-proyectos-cliente",
    }),
    ...route(`/lista-proyectos`, ListaProyectos, {
        Auth: true,
    }),
    ...route(`/lista-proyectos-cliente`, ListaProyectosAll, {
        Auth: true,
    }),
    ...route(`/lista-clientes`, ListaClientes, {
        Auth: true,
    }),

    ...route(`/registrar-presupuesto`, RegistrarPresupuesto, {
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

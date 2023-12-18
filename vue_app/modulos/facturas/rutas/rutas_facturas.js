import ListaFactura from '../componentes/ListaFactura'
import RegistrarFactura from '../componentes/RegistrarFactura'

const routes = [
    ...route(`/lista-facturas`, ListaFactura, {
        name: 'facturas',
        Auth: true,
    }),
    ...route(`/lista-facturas-pro`, ListaFactura, {
        name: 'pro-factura',
        Auth: true,
    }),
    ...route(`/registrr-facturas`, RegistrarFactura, {
        Auth: true,
    }),
    ...route(`/registrr-facturas-pro`, RegistrarFactura, {
        Auth: true,
    }),
]

function route(path, component = Default, meta = {}) {
    return [{
        path,
        component,
        meta
    }]
}

export default routes
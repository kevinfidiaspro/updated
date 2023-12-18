import FormProveedor from '../componentes/FormProveedor'
import ListaProveedores from '../componentes/ListaProveedores'

const routes = [
   ...route('/guardar-proveedor', FormProveedor, {
      Auth: true,
   }),
   ...route(`/lista-proveedores/${localStorage.getItem('user_id')}`, ListaProveedores, {
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

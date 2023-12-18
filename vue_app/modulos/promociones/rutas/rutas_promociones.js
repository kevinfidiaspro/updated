import FormPromociones from '../componentes/FormPromociones.vue'
import ListaPromociones from '../componentes/ListaPromociones.vue'

const routes = [
  ...route('/guardar-promocion', FormPromociones, {
     Auth: true,
     req_admin: true
  }),
  ...route('/lista-promociones', ListaPromociones, {
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

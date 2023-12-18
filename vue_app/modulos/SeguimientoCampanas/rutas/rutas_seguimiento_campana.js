
import FormSeguimiento from '../componentes/FormSeguimiento.vue'
import ListaSeguimiento from '../componentes/ListaSeguimiento.vue'
import ListaFases from '../componentes/ListaFases.vue'
import FormFases from '../componentes/FormFases.vue'

const routes = [

  ...route('/guardar-seguimiento-campana', FormSeguimiento, {
   Auth: true,
   req_admin: true
}),

  ...route('/lista-seguimiento-campana', ListaSeguimiento, {
   Auth: true,
}),
...route('/lista-fases-campana', ListaFases, {
  Auth: true,
}),
...route('/guardar-fases-campana', FormFases, {
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

import DatosEmpresa from '../componentes/DatosEmpresa.vue'

const routes = [
  ...route(`/datos-empresa/${localStorage.getItem('user_id')}`, DatosEmpresa, {
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
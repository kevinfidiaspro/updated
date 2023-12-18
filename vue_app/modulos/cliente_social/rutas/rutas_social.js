import ListaSocial from '../componentes/ListaSocial.vue'
import FormSocial from '../componentes/FormSocial.vue'
import ListaSocialCliente from '../componentes/ListaSocialCliente.vue'

const routes = [
	...route('/form-social', FormSocial, {
		Auth: true,
	 }),
  ...route('/lista-social', ListaSocial, {
     Auth: true,
  }),
  ...route('/social-cliente', ListaSocialCliente, {
	Auth: false,
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

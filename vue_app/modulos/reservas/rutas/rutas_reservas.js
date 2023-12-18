import ListaFormReservas from '../componentes/ListaFormReservas.vue'

const routes = [
  ...route('/lista-reservas', ListaFormReservas, {
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

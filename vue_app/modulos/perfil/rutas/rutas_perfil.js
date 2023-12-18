import FormPerfil from '../componentes/FormPerfil.vue'

const routes = [
  ...route('/perfil-usuario', FormPerfil, {
     Auth: true,
  })
]

function route(path, component = Default, meta = {}) {
	return [{
		path,
		component,
		meta
	}]
}

export default routes

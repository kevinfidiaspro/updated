import Tareas from '../componentes/Tareas'

const routes = [
   ...route(`/tareas`, Tareas, {
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
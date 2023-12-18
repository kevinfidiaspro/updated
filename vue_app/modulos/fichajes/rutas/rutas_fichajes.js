import ListaFichajes from '../componentes/ListaFichajes'
import FormFichajes from '../componentes/FormFichajes'

const routes = [
   ...route(`/lista-fichajes`, ListaFichajes, {
     Auth: true,    
  }),
  ...route(`/guardar-fichaje`, FormFichajes, {
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
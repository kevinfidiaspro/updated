import FormPassword from '../componentes/FormPassword'
import ListaPasswords from '../componentes/ListaPasswords'

const routes = [
   ...route('/guardar-password', FormPassword, {
      Auth: true,      
   }),
   ...route(`/lista-passwords`, ListaPasswords, {
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
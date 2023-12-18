import FormEmpresas from '../componentes/FormEmpresas'
import ListaEmpresas from '../componentes/ListaEmpresas'

const routes = [
   ...route('/guardar-empresa', FormEmpresas, {
      Auth: true,      
   }),
   ...route(`/lista-empresas`, ListaEmpresas, {
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
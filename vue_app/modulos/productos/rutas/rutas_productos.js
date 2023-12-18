import ListaProducto from '../componentes/ListaProducto.vue'
import FormProducto from '../componentes/FormProducto.vue'



const routes = [
    ...route('/guardar-producto', FormProducto, {
        Auth: true,
        ///req_admin: true
     }),
     ...route('/lista-producto', ListaProducto, {
       Auth: true,
       //req_admin: true
    }),]

function route(path, component = Default, meta = {}) {
	return [{
		path,
		component,
		meta
	}]
}

export default routes
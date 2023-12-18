import CalendarioReuniones from '../componentes/CalendarioReuniones.vue'
import FormReuniones from '../componentes/FormReuniones.vue'


const routes = [
    ...route('/guardar-reuniones', FormReuniones, {
        Auth: true,
        ///req_admin: true
     }),
     ...route('/calendario-reuniones', CalendarioReuniones, {
        Auth: true,
        //req_admin: true
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
import GestionGlobal from '../componentes/GestionGlobal.vue'

import GestorDocumentos from '../componentes/GestorDocumentos.vue'
import ViewerDocument from '../componentes/ViewerDocument.vue'

import AdministradorTareasGlobal from '../componentes/administrador_tareas/AdministradorTareasGlobal.vue'

const routes = [

	//Gestor de archivos
  ...route(`/gestor-documentos/${localStorage.getItem('user_id')}`, GestionGlobal, {
     Auth: true,
  }),
  ...route('/gestor-documentos/:id', GestorDocumentos, {
	Auth: true,
	
 }),

 //Visor de documetos 
  ...route('/visor-documentos/:id/:document/:format/:path', ViewerDocument, {
	Auth: true,
	
 }),

  //Administrador de tareas  
  // :id user_id
  ...route('/administrador-tareas/:id', AdministradorTareasGlobal, {

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
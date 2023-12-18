import FormAlbaran from '../componentes/FormAlbaran'
import ListaAlbaranes from '../componentes/ListaAlbaranes'
import AlbaranesEnviados from '../componentes/AlbaranesEnviados'
import AlbaranesRecibidos from '../componentes/AlbaranesRecibidos'
import FormAlbaranesEnviados from '../componentes/FormAlbaranesEnviados'
import FormAlbaranesRecibido from '../componentes/FormAlbaranesRecibido'
import FormAlbaranesRecibidoUpdate from '../componentes/FormRecibidosUpdate'
import FormEnviadosUpdate from '../componentes/FormEnviadosUpdate'

const routes = [
   ...route('/guardar-albaran', FormAlbaran, {
      Auth: true,      
   }),
   ...route(`/lista-albaranes/${localStorage.getItem('user_id')}`, ListaAlbaranes, {
     Auth: true,    
  }),
    ...route(`/lista-albaranes-enviados/${localStorage.getItem('user_id')}`, AlbaranesEnviados, {
     Auth: true,    
  }),
     ...route(`/lista-albaranes-recibidos/${localStorage.getItem('user_id')}`, AlbaranesRecibidos, {
     Auth: true,    
  }),
  ...route(`/form-albaranes-enviados/${localStorage.getItem('user_id')}`, FormAlbaranesEnviados, {
     Auth: true,    
  }),
  ...route(`/form-albaranes-recibidos/${localStorage.getItem('user_id')}`, FormAlbaranesRecibido, {
     Auth: true,    
  }),
  ...route(`/form-albaranes-recibidos-update/${localStorage.getItem('user_id')}/:idAlbaran`, FormAlbaranesRecibidoUpdate, {
     Auth: true,    
  }),
   ...route(`/form-albaranes-enviados-update/${localStorage.getItem('user_id')}/:idAlbaran`, FormEnviadosUpdate, {
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

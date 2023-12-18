import Tickets from '../componentes/Tickets'
import FormTicket from '../componentes/FormTicket.vue'
import FormTicketNoLogin from "../componentes/FormTicketNoLogin.vue"; 

const routes = [
   	...route(`/tickets`, Tickets, {
     Auth: true,    
	}),
	...route('/guardar-ticket', FormTicket, {
		Auth: true,
	}),
	...route('/editar-ticket', FormTicket, {
		Auth: true,
	}),
	
    ...route('/crear-ticket', FormTicketNoLogin),
]

function route(path, component = Default, meta = {}) {
	return [{
		path,
		component,
		meta
	}]
}

export default routes
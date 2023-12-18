import FormRecibo from '../componentes/FormRecibo'
import ListaRecibos from '../componentes/ListaRecibos'
import ListaFacturas from '../componentes/ListaFacturas'
import ListaNotas from '../componentes/ListaNotas'
import ListaParteTrabajo from '../componentes/ListaParteTrabajo'


import ListaFacturasRecibidas from '../componentes/ListaFacturasRecibidas'
import FormFacturasRecibidas from '../componentes/FormFacturasRecibidas'
import FormFacturasRecibidasUpdate from '../componentes/FormFacturasRecibidasUpdate'



const routes = [
   ...route('/guardar-recibo', FormRecibo, {
      Auth: true,
     
   }),
   ...route(`/lista-recibos/${localStorage.getItem('user_id')}`, ListaRecibos, {
      Auth: true,
      
   }),
   ...route(`/lista-facturas/${localStorage.getItem('user_id')}`, ListaFacturas, {
      Auth: true,
      
   }),

   ...route(`/lista-facturas-recibidas/${localStorage.getItem('user_id')}`, ListaFacturasRecibidas, {
      Auth: true,
      
   }),
   ...route(`/form-facturas-recibidas/${localStorage.getItem('user_id')}`, FormFacturasRecibidas, {
      Auth: true,
      
   }),
   ...route(`/form-facturas-recibidas-update/:idFacturaRec`, FormFacturasRecibidasUpdate, {
      Auth: true,
      
   }),

   ...route(`/lista-notas/${localStorage.getItem('user_id')}`, ListaNotas, {
      Auth: true,
      
   }),
   ...route(`/lista-parte-trabajo/${localStorage.getItem('user_id')}`, ListaParteTrabajo, {
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

import ListaMorosos from '../componentes/ListaMorosos'

const routes = [
   ...route(`/morosos/${localStorage.getItem('user_id')}`, ListaMorosos, {
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

import FormVideo from '../componentes/FormVideo.vue'
import ListaVideos from '../componentes/ListaVideos'

const routes = [
  ...route('/guardar-video', FormVideo, {
     Auth: true,
     req_admin: true
  }),
  ...route('/lista-videos', ListaVideos, {
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

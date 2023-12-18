import FacebookLogin from './FacebookLogin.vue'



const routes = [
    ...route('/facebook', FacebookLogin, {
        Auth: true,
        req_admin: true
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
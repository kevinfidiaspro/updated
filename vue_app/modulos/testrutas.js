import test from './test.vue'

const routes = [
  ...route(`/test`, test, {
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
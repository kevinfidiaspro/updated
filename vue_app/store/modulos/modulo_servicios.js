const modulo_servicios = {
	strict: false,

  state: {
     servicios:[]
  },	

	getters: {
		get_servicios: state => state.servicios,
	},

	mutations: {
		set_servicios: (state, lista_servicios) => state.servicios = lista_servicios,
	},

	actions: {
		setServicios: (context, lista_servicios) => context.commit('set_servicios', lista_servicios),
	}
}

export default modulo_servicios;

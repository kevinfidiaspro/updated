const modulo_estados = {
	strict: false,

  state: {
     estados:[]
  },	

	getters: {
		get_estados: state => state.estados,
	},

	mutations: {
		set_estados: (state, lista_estados) => state.estados = lista_estados,
	},

	actions: {
		setEstados: (context, lista_estados) => context.commit('set_estados', lista_estados),
	}
}

export default modulo_estados;

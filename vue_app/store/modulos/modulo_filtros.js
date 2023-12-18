const modulo_filtros = {
    strict: false,

    state: {
        potenciales: {
            page: 1,
            amount: 15,
            datestart: null,
            dateend: null,
            estado: null,
            campain: null,
        },
    },

    getters: {
        get_filtros_potenciales: (state) => state.potenciales,
    },

    mutations: {
        set_filtros_potenciales: (state, filtros) =>
            (state.potenciales = filtros),
    },

    actions: {
        setFiltrosPotenciales: (context, filtros) =>
            context.commit("set_filtros_potenciales", filtros),
    },
};

export default modulo_filtros;

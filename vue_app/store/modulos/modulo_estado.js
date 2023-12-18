const modulo_estado = {
    strict: false,

    state: {
        is_loading: false,

        auth: false,

        user: {
            name: "...",
            role: 2,
            email: "",
            id: "",
        },

        errors: {
            errors: {},
        },
    },

    getters: {
        getloading: (state) => state.is_loading,

        geterrors: (state) => state.errors,

        getuser: (state) => state.user,

        getauth: (state) => state.auth,
    },

    mutations: {
        is_loading: (state, status) => (state.is_loading = status),

        is_errors: (state, errors) => (state.errors = errors),

        user: (state, user) => (state.user = user),

        auth: (state, status) => (state.auth = status),
    },

    actions: {
        isLoading: (context, status) => context.commit("is_loading", status),

        setErrors: (context, errors) => context.commit("is_errors", errors),

        setUser: (context, user) => context.commit("user", user),

        setAuth: (context, status) => context.commit("auth", status),
    },
};

export default modulo_estado;

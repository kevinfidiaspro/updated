<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card shaped class="mx-4 my-4 pa-4">
            <v-row>
                <v-col cols="12">
                    <v-toolbar
                        flat
                        color="#1d2735"
                        dark
                        style="border-radius: 5px"
                    >
                        <v-icon class="white--text" style="font-size: 45px"
                            >mdi-account-supervisor-circle</v-icon
                        >
                        <pre><v-toolbar-title><h2>Calendario Seguimientos</h2></v-toolbar-title></pre>
                    </v-toolbar>
                    <v-tooltip right>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                fab
                                :to="{ path: `/lista-seguimiento` }"
                                :loading="isloading"
                                :disabled="isloading"
                                color="primary darken-1"
                                class="mt-2"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon class="white--text"
                                    >mdi-format-list-bulleted</v-icon
                                >
                            </v-btn>
                        </template>
                        <span>Listado de seguimientos</span>
                    </v-tooltip>
                    <v-form class="mt-3"> </v-form>
                </v-col>
            </v-row>
            <v-row> </v-row>
            <v-row>
                <v-col cols="12">
                    <cita-picker
                        @CambioFecha="CambioFecha"
                        v-model="Seguimientos"
                    ></cita-picker>
                </v-col>
            </v-row>
        </v-card>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            rol: 0,
            clientes: [],
            seguimiento: {},
            agentes: {},
            Seguimientos: [],
            controles: [],
            acciones: [],
            productos: [],
        };
    },

    created() {
        //this.getSeguimientos();
    },
    methods: {
        CambioFecha(week) {
            console.log(week);
            if (week.length >= 7) this.getSeguimientos(week[0], week[6]);
        },
        getSeguimientos(desde, hasta) {
            const self = this;
            axios
                .get(
                    `api/get-tareas-unicas-proyecto?desde=${desde}&hasta=${hasta}&${
                        this.$route.meta.potencial
                            ? "potencial_only"
                            : "cliente_only"
                    }=true`
                )
                .then(
                    (res) => {
                        self.Seguimientos = res.data;
                    },
                    (err) => {
                        this.$toast.error("Error consultando Seguimientos");
                    }
                );
        },
        formatNumber(number) {
            return Number.parseFloat(number.toString())
                .toFixed(2)
                .replace(".", ",")
                .replace(",00", "");
        },
    },
    filters: {},

    computed: {
        isloading() {
            return this.$store.getters.getloading;
        },
        provincias() {
            return this.$store.getters.getprovincias;
        },
    },
};
</script>
<style>
.rowtwo {
    display: flex;
    flex-direction: row;
}
.spacing {
    padding-top: 0 !important;
}
</style>

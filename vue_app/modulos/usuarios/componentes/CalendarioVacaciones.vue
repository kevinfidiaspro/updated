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
                        <pre><v-toolbar-title><h2>Calendario Vacaciones</h2></v-toolbar-title></pre>
                    </v-toolbar>
                    <v-tooltip right>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                fab
                                :loading="isloading"
                                :disabled="isloading"
                                color="primary darken-1"
                                class="mt-2"
                                v-bind="attrs"
                                v-on="on"
                                @click="dialog = true"
                            >
                                <v-icon class="white--text"
                                    >mdi-format-list-bulleted</v-icon
                                >
                            </v-btn>
                        </template>
                        <span>Agregar Vacaciones</span>
                    </v-tooltip>
                    <v-form class="mt-3"> </v-form>
                </v-col>
            </v-row>
            <v-row> </v-row>
            <v-row>
                <v-col cols="12">
                    <vacacionesPickerVue
                        v-model="Seguimientos"
                        @getWeek="getSeguimientos"
                    ></vacacionesPickerVue>
                </v-col>
            </v-row>
        </v-card>
        <v-dialog v-model="dialog">
            <v-card>
                <v-card-title
                    class="text-h5 aviso"
                    style="
                        justify-content: center;
                        background: #1d2735;
                        color: white;
                    "
                >
                    Guardar Vacaciones
                </v-card-title>
                <v-card-text style="text-align: center">
                    <Vacaciones
                        @update="() => getSeguimientos(null)"
                        :user="user"
                    ></Vacaciones>
                </v-card-text>
                <v-card-actions class="pt-3">
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        large
                        @click="
                            dialog = false;
                            selectedItem = {};
                        "
                        >Cerrar</v-btn
                    >

                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
import Vacaciones from "../componentes/Vacaciones.vue";
import vacacionesPickerVue from "../../../components/general/planpicker/vacacionesPicker.vue";
export default {
    components: { vacacionesPickerVue, Vacaciones },
    data() {
        return {
            dialog: false,
            rol: 0,
            clientes: [],
            seguimiento: {},
            agentes: {},
            Seguimientos: [],
            controles: [],
            acciones: [],
            productos: [],
            current_fecha: null,
        };
    },

    created() {
        //this.getSeguimientos(Date.now());
    },
    methods: {
        getSeguimientos(year) {
            const self = this;
            axios.get(`api/get-vacaciones/${year}`).then(
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
        user() {
            return this.$store.getters.getuser;
        },
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

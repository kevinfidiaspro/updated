<template>
    <v-card shaped class="pa-4">
        <v-row>
            <v-col cols="12">
                <v-form class="mt-3">
                    <v-row>
                        <v-col cols="12">
                            <date-select label="Dia" v-model="gasto.dia">
                            </date-select>
                        </v-col>
                        <v-col cols="12">
                            <v-checkbox
                                v-model="gasto.sin_factura"
                                label="Sin Factura"
                            ></v-checkbox>
                        </v-col>
                        <v-col cols="12">
                            <v-autocomplete
                                v-model="gasto.id_seguimiento"
                                :items="seguimientos"
                                item-value="id"
                                item-text="nombre"
                                label="Seguimientos"
                            >
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="12">
                            <v-autocomplete
                                v-if="gasto.sin_factura == 1"
                                prepend-icon="mdi-account-search-outline"
                                v-model="gasto.id_cliente"
                                :items="usuarios"
                                item-value="id"
                                item-text="nombre"
                                label="Cliente"
                            >
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="12">
                            <v-autocomplete
                                v-if="gasto.sin_factura != 1"
                                label="Factura"
                                :items="tipos"
                                item-text="tipo_nro"
                                item-value="id"
                                v-model="gasto.id_factura"
                            >
                            </v-autocomplete>
                            <v-text-field
                                v-else
                                label="Total"
                                v-model="gasto.total_sf"
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12">
                            <v-btn
                                v-if="!gasto.id"
                                :disabled="isloading"
                                color="success"
                                class="white--text"
                                @click="saveGasto"
                                >Guardar</v-btn
                            >
                            <v-btn
                                @click="saveGasto"
                                v-if="gasto.id"
                                :disabled="isloading"
                                color="success"
                                class="white--text"
                                >Actualizar</v-btn
                            >
                            <v-btn
                                @click="deleteGasto"
                                v-if="gasto.id"
                                :disabled="isloading"
                                color="red"
                                class="white--text"
                                >Eliminar</v-btn
                            >
                        </v-col>
                    </v-row>
                </v-form>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
export default {
    props: ["value", "tipos"],
    data() {
        return {
            seguimientos: [],

            usuarios: [],
            save_tipo: {},
            gasto: { gasto: 0 },
        };
    },
    watch: {
        value(val) {
            this.gasto = val;
        },
        gasto(val) {
            this.$emit("input", val);
        },
    },
    created() {
        if (this.value != null) this.gasto = this.value;
        this.getUsuarios();
        this.getSeguimientos();
    },
    methods: {
        getSeguimientos() {
            axios.get(`api/get-seguimientos`).then(
                (res) => {
                    this.seguimientos = res.data;
                },
                (err) => {
                    this.$toast.error("Error consultando seguimientos");
                }
            );
        },
        getUsuarios() {
            axios.get(`api/get-usuarios`).then(
                (res) => {
                    this.usuarios = res.data.users;
                },
                (err) => {
                    this.$toast.error("Error consultando clientes");
                }
            );
        },
        saveGasto() {
            const self = this;
            axios
                .post("api/save-venta-diaria", this.gasto)
                .then(function (response) {
                    self.$emit("close_modal");
                });
        },
    },
    filters: {},
    computed: {
        user() {
            return this.$store.getters.getuser;
        },
        isloading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>

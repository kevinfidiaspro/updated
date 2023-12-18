<template>
    <v-card shaped class="pa-4">
        <v-row>
            <v-col cols="12">
                <v-form class="mt-3">
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field
                                label="Núm Acuerdo"
                                v-model="gasto.num_acuerdo"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                label="Importe"
                                v-model="gasto.importe"
                                type="number"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                label="IVA"
                                v-model="gasto.iva"
                                type="number"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-autocomplete
                                label="Razón Social"
                                :items="usuarios"
                                item-text="nombres"
                                item-value="id"
                                v-model="gasto.id_cliente"
                            >
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-autocomplete
                                label="Categoria"
                                :items="categorias"
                                item-text="nombre"
                                item-value="id"
                                v-model="gasto.id_categoria"
                            >
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="12" md="6">
                            <date-select
                                label="Fecha Firma"
                                v-model="gasto.fecha_firma"
                            >
                            </date-select>
                        </v-col>
                        <v-col cols="12" md="6">
                            <date-select
                                label="Fecha IVA Pagado"
                                v-model="gasto.fecha_iva"
                            >
                            </date-select>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                label="Bono digital"
                                v-model="gasto.bono_digital"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <date-select
                                label="Justificación 1"
                                v-model="gasto.justificacion_1"
                            >
                            </date-select>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                label="Cobro 1"
                                v-model="gasto.cobro_1"
                                type="number"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <date-select
                                label="Justificación 2"
                                v-model="gasto.justificacion_2"
                            >
                            </date-select>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                label="Cobro 2"
                                v-model="gasto.cobro_2"
                                type="number"
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
                        </v-col>
                    </v-row>
                </v-form>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
export default {
    props: ["value", "categorias", "usuarios", "estados"],
    data() {
        return {
            save_tipo: {},
            gasto: { gasto: 0, iva: 21 },
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
    },
    methods: {
        saveGasto() {
            const self = this;
            axios
                .post("api/save-venta-kit", this.gasto)
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

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
                            <v-text-field
                                label="Código"
                                v-model="gasto.codigo"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-autocomplete
                                label="Web"
                                :items="tipos"
                                item-text="nombre"
                                item-value="id"
                                v-model="gasto.id_empresa"
                            >
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                type="number"
                                label="Precio"
                                v-model="gasto.precio"
                            >
                            </v-text-field>
                        </v-col>
                        <v-col
                            cols="12"
                            v-if="user.rol_tfg == 1 || user.rol_tfg == 4"
                        >
                            <v-text-field
                                type="number"
                                label="Gasto"
                                v-model="gasto.gasto"
                            >
                            </v-text-field>
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
    },
    methods: {
        getTipos() {
            this.$emit("getTipos");
        },
        saveGasto() {
            const self = this;
            axios
                .post("api/save-ventas-tfg", this.gasto)
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

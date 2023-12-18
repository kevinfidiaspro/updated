<template>
    <v-card shaped class="pa-4">
        <v-row>
            <v-col cols="12">
                <v-form class="mt-3">
                    <v-row>
                        <v-col cols="12">
                            <date-select
                                label="Mes"
                                type="month"
                                v-model="gasto.mes"
                            ></date-select>
                        </v-col>
                        <v-col cols="12">
                            <v-autocomplete
                                label="Web"
                                :items="tipos"
                                item-text="name"
                                item-value="id"
                                v-model="gasto.id_web"
                            >
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                type="number"
                                label="Invertido"
                                v-model="gasto.invertido"
                            >
                            </v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                type="number"
                                label="Clics"
                                v-model="gasto.clics"
                            >
                            </v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                type="number"
                                label="CTR"
                                v-model="gasto.ctr"
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
            gasto: {},
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
                .post("api/save-marketing", this.gasto)
                .then(function (response) {
                    self.$emit("close_modal");
                });
        },
    },
    filters: {},
    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>

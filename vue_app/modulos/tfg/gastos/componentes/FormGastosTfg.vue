<template>
    <v-card shaped class="pa-4">
        <v-row>
            <v-col cols="12">
                <v-form class="mt-3">
                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                                type="number"
                                label="Euros"
                                v-model="gasto.euros"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                label="Descripcion"
                                v-model="gasto.descripcion"
                            >
                            </v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <dynamic_select
                                v-model="gasto.id_tipo"
                                title="Tipo"
                                show="nombre"
                                item-value="id"
                                :estados="tipos"
                                @delete="deleteProyectoEstado"
                                @create="saveproyectoEstado"
                                @clear="save_tipo = {}"
                                @update="saveproyectoEstado"
                                @getEstado="
                                    (index) => {
                                        save_tipo = tipos[index];
                                    }
                                "
                            >
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field
                                            label="Nombre"
                                            v-model="save_tipo.nombre"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </dynamic_select>
                        </v-col>

                        <v-col cols="12">
                            <date-select label="Fecha" v-model="gasto.mes">
                            </date-select>
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
        deleteProyectoEstado(id) {
            axios.post("api/delete-tfg-gasto-tipo", { id: id }).then((res) => {
                this.getTipos();
            });
        },
        saveproyectoEstado() {
            axios
                .post("api/save-tfg-gasto-tipo", this.save_tipo)
                .then((res) => {
                    this.getTipos();
                    this.save_tipo = {};
                });
        },
        getTipos() {
            this.$emit("getTipos");
        },
        saveGasto() {
            const self = this;
            axios
                .post("api/save-tfg-gasto", this.gasto)
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

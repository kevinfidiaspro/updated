<template>
    <v-container>
        <v-card shaped class="mx-4 my-4 pa-4">
            <v-toolbar flat color="#1d2735" dark>
                <v-toolbar-title>Guardar / Editar Plantilla</v-toolbar-title>
            </v-toolbar>
            <v-row>
                <v-col cols="12">
                    <v-form class="mt-3">
                        <h5>
                            nota: Añadir {{ brackets(1) }} para el nombre del
                            cliente, {{ brackets(2) }} para el nombre de
                            usuario, {{ brackets(3) }}
                            para el producto
                        </h5>
                        <v-row>
                            <v-col cols="12">
                                <v-textarea
                                    outlined
                                    label="Texto"
                                    v-model="plantilla.nombre"
                                >
                                </v-textarea>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="12">
                                <v-btn
                                    v-if="!plantilla.id"
                                    :disabled="isloading"
                                    color="success"
                                    class="white--text"
                                    @click="savePlantilla"
                                    >Guardar</v-btn
                                >
                                <v-btn
                                    @click="savePlantilla"
                                    v-if="plantilla.id"
                                    :disabled="isloading"
                                    color="success"
                                    class="white--text"
                                    >Actualizar</v-btn
                                >
                                <v-btn
                                    @click="deletePlantilla"
                                    v-if="plantilla.id"
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
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            proyectos: [],

            plantilla: {},
        };
    },
    watch: {},
    created() {
        if (this.$route.query.id) {
            this.getPlantilla(this.$route.query.id);
        }
    },
    methods: {
        brackets(number) {
            return `{{${number}}}`;
        },
        getPlantilla(id) {
            const self = this;
            axios.get(`api/get-plantilla/${id}`).then(function (response) {
                self.plantilla = response.data;
            });
        },
        savePlantilla() {
            const self = this;
            axios
                .post("api/save-plantilla", this.plantilla)
                .then(function (response) {
                    self.$router.push("lista-plantilla");
                });
        },
    },
    filters: {},
};
</script>

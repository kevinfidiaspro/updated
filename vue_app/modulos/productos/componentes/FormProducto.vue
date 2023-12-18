<template>
    <v-container>
        <v-card shaped class="mx-4 my-4 pa-4">
            <v-row>
                <v-col cols="12">
                    <v-form class="mt-3">
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    label="Nombre"
                                    v-model="producto.nombre"
                                >
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    label="Precio"
                                    type="number"
                                    v-model="producto.precio"
                                >
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    label="Minutos"
                                    type="number"
                                    v-model="producto.minutos"
                                >
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-autocomplete
                                    label="Modulo"
                                    v-model="producto.id_modulo"
                                    :items="modulos"
                                    item-text="nombre"
                                    item-value="id"
                                >
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea
                                    label="Descripcion"
                                    v-model="producto.descripcion"
                                >
                                </v-textarea>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="12">
                                <v-btn
                                    v-if="!producto.id"
                                    :disabled="isloading"
                                    color="success"
                                    class="white--text"
                                    @click="saveProducto"
                                    >Guardar</v-btn
                                >
                                <v-btn
                                    @click="saveProducto"
                                    v-if="producto.id"
                                    :disabled="isloading"
                                    color="success"
                                    class="white--text"
                                    >Actualizar</v-btn
                                >
                                <v-btn
                                    @click="deleteProducto"
                                    v-if="producto.id"
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
            modulos: [],
            producto: {},
        };
    },
    watch: {},
    created() {
        this.getModulos();
        if (this.$route.query.id) {
            this.getProducto(this.$route.query.id);
        }
    },
    methods: {
        getModulos() {
            const self = this;
            axios.get(`api/get-productos-modulos`).then(function (response) {
                self.modulos = response.data;
            });
        },
        getProducto(id) {
            const self = this;
            axios.get(`api/get-producto/${id}`).then(function (response) {
                self.producto = response.data;
            });
        },
        saveProducto() {
            const self = this;
            axios
                .post("api/save-producto", this.producto)
                .then(function (response) {
                    self.$router.push("lista-producto");
                });
        },
    },
    filters: {},
};
</script>

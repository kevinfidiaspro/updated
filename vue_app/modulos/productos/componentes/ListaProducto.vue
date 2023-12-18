<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-account-supervisor-circle</v-icon
            >
            <pre><v-toolbar-title><h2>Lista Productos</h2></v-toolbar-title></pre>
        </v-toolbar>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    :to="{ path: `/guardar-producto` }"
                    :loading="isloading"
                    :disabled="isloading"
                    color="orange darken-1"
                    class="mt-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text"
                        >mdi-account-plus-outline</v-icon
                    >
                </v-btn>
            </template>
            <span>Nuevo Producto</span>
        </v-tooltip>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field
                    prepend-icon="mdi-account-search"
                    v-model="search"
                    label="Buscar"
                ></v-text-field>
            </v-col>
        </v-row>
        <template>
            <v-row justify="center">
                <v-expansion-panels accordion>
                    <v-expansion-panel
                        v-for="(producto, i) in productos"
                        :key="i"
                    >
                        <v-expansion-panel-header>{{
                            producto.nombre
                        }}</v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-data-table
                                dense
                                :headers="headers"
                                :items="producto.items"
                                :search="search"
                                disable-pagination
                                hide-default-footer
                                :items-per-page="15"
                                item-key="id"
                                class="elevation-1"
                                :sort-by="['nombre']"
                                :sort-desc="[false]"
                            >
                                <template v-slot:item.precio="{ item }">
                                    {{ item.precio }} €
                                </template>

                                <template v-slot:item.action="{ item }">
                                    <router-link
                                        :to="{
                                            path: `/guardar-producto?id=${item.id}`,
                                        }"
                                        class="action-buttons"
                                    >
                                        <v-icon
                                            small
                                            class="mr-2"
                                            color="#1d2735"
                                            style="font-size: 25px"
                                            title="EDITAR"
                                            >mdi-pencil-outline</v-icon
                                        >
                                    </router-link>
                                    <v-icon
                                        @click="openModal(item)"
                                        small
                                        class="mr-2"
                                        color="red"
                                        style="font-size: 25px"
                                        title="BORRAR"
                                        >mdi-trash-can</v-icon
                                    >
                                </template>
                            </v-data-table>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </v-row>
        </template>

        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title
                    class="text-h5 aviso"
                    style="
                        justify-content: center;
                        background: #1d2735;
                        color: white;
                    "
                >
                    Aviso
                </v-card-title>
                <v-card-text style="text-align: center">
                    <h2>¿Estás seguro que deseas eliminar?</h2>
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
                        >Cancelar</v-btn
                    >
                    <v-btn color="success" large @click="deleteUser()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>
<script>
export default {
    data() {
        return {
            search: "",
            headers: [
                { text: "Nombre", value: "nombre", sortable: false },
                { text: "Descripcion", value: "descripcion", sortable: false },
                { text: "Precio", value: "precio", sortable: false },

                { text: "Minutos", value: "minutos", sortable: false },

                { text: "Acciones", value: "action", sortable: false },
            ],
            productos: [],
            selectedItem: 0,
            dialog: false,
        };
    },
    created() {
        this.getProductos();
    },
    methods: {
        getProductos() {
            const self = this;
            axios.get(`api/get-productos`).then(
                (res) => {
                    self.productos = res.data;
                },
                (err) => {
                    this.$toast.error("Error consultando Productos");
                }
            );
        },
        openModal(item) {
            this.selectedItem = item;
            this.dialog = true;
        },
        deleteUser() {
            axios
                .post("api/delete-producto", {
                    id: this.selectedItem.id,
                })
                .then(
                    (res) => {
                        this.$toast.sucs("Producto eliminado");
                        this.dialog = false;
                        this.getProductos();
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Producto");
                    }
                );
        },
    },
    computed: {
        isloading: function () {
            // return this.$store.getters.getloading
        },
    },
};
</script>

<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-microsoft-powerpoint</v-icon
            >
            <pre><v-toolbar-title><h2>Lista Proyectos</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    :to="'/lista-clientes'"
                    :loading="isloading"
                    :disabled="isloading"
                    color="blue"
                    class="mt-2 mx-3"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text"
                        >mdi-arrow-left-bold-outline</v-icon
                    >
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    :to="{
                        path: `/guardar-proyecto?clienteid=${clienteIdProyect}&tipo=${$route.query.tipo}`,
                    }"
                    :loading="isloading"
                    :disabled="isloading"
                    color="orange darken-1"
                    class="mt-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text">mdi-plus-box</v-icon>
                </v-btn>
            </template>
            <span>Nuevo Proyecto</span>
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
        <v-data-table
            dense
            :headers="headers"
            :items="proyectos"
            :search="search"
            :items-per-page="15"
            item-key="id"
            class="elevation-0"
            :sort-by="['usuario.nombre']"
            :sort-desc="[false]"
        >
            <template v-slot:item.usuario.nombre="{ item }">
                <span v-if="item.usuario">{{ item.usuario.nombre }}</span>
                <span v-else style="color: red !important"
                    ><strong>Cliente Eliminado</strong></span
                >
            </template>
            <template v-slot:item.action="{ item }">
                <router-link
                    :to="{
                        path: `/guardar-proyecto?id=${item.id}&tipo=${$route.query.tipo}`,
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
                    <v-btn color="success" large @click="deleteProyecto()"
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
            proyectos: [],
            selectedItem: 0,
            clienteIdProyect: 0,
            dialog: false,
            headers: [
                { text: "Proyecto", value: "nombre", sortable: false },
                { text: "Producto", value: "servicio.nombre", sortable: false },
                { text: "%", value: "porc_realizado", sortable: false },
                { text: "Pvp (€)", value: "pvp", sortable: false },
                { text: "Fecha Alta", value: "fecha_alta", sortable: false },
                { text: "Acciones", value: "action", sortable: false },
            ],
        };
    },
    created() {
        this.clienteIdProyect = this.$route.query.id;
        if (this.$route.query.id) {
            this.getProyectosByUser(
                this.$route.query.id,
                this.$route.query.tipo
            );
        } else {
            this.$toast.error("Error consultando proyectos del cliente");
        }
    },
    methods: {
        getAllProyectos() {
            axios.get(`api/get-all-proyectos`).then(
                (res) => {
                    this.proyectos = res.data;
                    console.log(this.proyectos);
                },
                (res) => {
                    this.$toast.error("Error consultando proyectos");
                }
            );
        },
        getProyectosByUser(userId, tipo) {
            axios.get(`api/get-proyectos-by-user-id/${userId}/${tipo}`).then(
                (res) => {
                    this.proyectos = res.data;
                },
                (res) => {
                    this.$toast.error("Error consultando proyectos");
                }
            );
        },
        deleteProyecto() {
            this.dialog = false;
            axios
                .get(
                    `api/delete-proyecto/${
                        this.proyectos[this.selectedItem].id
                    }`
                )
                .then(
                    (res) => {
                        this.proyectos.splice(this.selectedItem, 1);
                        this.$toast.sucs("Proyecto eliminado");
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Proyecto");
                    }
                );
        },
        openModal(item) {
            this.selectedItem = this.proyectos.indexOf(item);
            this.dialog = true;
        },
    },
    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>
